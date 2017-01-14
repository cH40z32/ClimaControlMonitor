<?php

require_once ("class.Database.php");
class DataAccess extends Database
{
	private $SleepDelay = 50000;
	private $Timeout = 50000000;
	function __construct($servername, $database, $username, $password)
	{
		parent::__construct($servername, $database, $username, $password);
	}

	public function AddMeasure($temperature, $humidity, $wantedTemperature, $wantedHumidity, $IsVentilating, $isIlluminating, $isChannelAActive, $isChannelBActive)
	{

		$sql = "insert into Measures (Temperature,Humidity,WantedTemperature,WantedHumidity,IsVentilating,IsIlluminating,IsChannelAActive,IsChannelBActive ) 
	 			VALUES($temperature,$humidity, $wantedTemperature, $wantedHumidity, $IsVentilating,$isIlluminating,$isChannelAActive,$isChannelBActive);";
		$this -> databaseHandle -> exec($sql);

	}

	public function GetMeasures($from, $to)
	{

		$sth = $this -> databaseHandle -> prepare("SELECT * FROM
						(
							select 
							unix_timestamp(Timestamp)*1000 as Timestamp,
							Temperature,
							Humidity,
							WantedTemperature,
							WantedHumidity,
							ORD(IsVentilating) as IsVentilating,
							ORD(IsIlluminating) as IsIlluminating,
							ORD(IsChannelAActive) as IsChannelAActive,
							ORD(IsChannelBActive) as IsChannelBActive
							from Measures 
							where unix_timestamp(Timestamp)>=$from && unix_timestamp(Timestamp)<=$to
							order by timestamp desc limit 1000
						) as T1 order by timestamp asc");

		$sth -> execute();

		/* Group values by the first column */
		return $sth -> fetchAll(PDO::FETCH_ASSOC);
	}

	public function GetLastMeasure($lasttimestamp = null)
	{
		$elapsedTime = 0;
		do
		{

			$sth = $this -> databaseHandle -> prepare("SELECT 
							unix_timestamp(Timestamp)*1000 as Timestamp,
							Temperature,
							Humidity,
							WantedTemperature,
							WantedHumidity,
							ORD(IsVentilating) as IsVentilating,
							ORD(IsIlluminating) as IsIlluminating,
							ORD(IsChannelAActive) as IsChannelAActive,
							ORD(IsChannelBActive) as IsChannelBActive
							from Measures 
							order by timestamp desc limit 1");

			$sth -> execute();

			/* Group values by the first column */
			$dataset = $sth -> fetch(PDO::FETCH_ASSOC);

			usleep($this -> SleepDelay);
			$elapsedTime += $this -> SleepDelay;
			if ($elapsedTime > $this -> Timeout)
			{
				echo "elapsed";
				return null;
			}

			if ($lasttimestamp == null)
				return $dataset;

		}
		while($dataset['Timestamp']<=$lasttimestamp);
		return $dataset;
	}

	public function GetTimeSpans()
	{

		$sth = $this -> databaseHandle -> prepare("
							select 
							Channel,
							Start,
							End
							from TimeSpans 
							order by Channel desc");

		$sth -> execute();
		/* Group values by the first column */
		return $sth -> fetchAll(PDO::FETCH_ASSOC);

	}

	public function GetWantedTemperature()
	{

		$sth = $this -> databaseHandle -> prepare("
							select 
							Timestamp,
							Value
							from WantedValues 
							where Channel = 'Temperature'
							order by Channel desc,Timestamp desc");

		$sth -> execute();
		/* Group values by the first column */
		return $sth -> fetchAll(PDO::FETCH_ASSOC);
	}

	public function GetWantedHumidity()
	{

		$sth = $this -> databaseHandle -> prepare("
							select 
							Timestamp,
							Value
							from WantedValues 
							where Channel = 'Humidity'
							order by Channel desc,Timestamp desc");

		$sth -> execute();
		/* Group values by the first column */
		return $sth -> fetchAll(PDO::FETCH_ASSOC);
	}

	public function SetTimeSpans($ranges)
	{
		$sth = $this -> databaseHandle -> prepare("delete from TimeSpans");
	$sth -> execute();
		foreach ($ranges as $range)
		{
			$sth = $this -> databaseHandle -> prepare("insert into TimeSpans 
							(Channel,Start,End)
							Values('" . $range[0] . "','" . $this->LocalDate($range[1] ). "','" . $this->LocalDate($range[2] ). "')");

			$sth -> execute();
		}
	}

	public function SetWantedHumidity($ranges)
	{
		$sth = $this -> databaseHandle -> prepare("delete from WantedValues where Channel='Humidity'");

		$sth -> execute();

		foreach ($ranges as $range)
		{
			
			$sth = $this -> databaseHandle -> prepare("insert into WantedValues
								(Channel,Timestamp,Value)
								Values('Humidity','" . $this->LocalDate($range[0]) . "'," . $range[1] . ")");

			$sth -> execute();
		}

	}

	public function SetWantedTemperature($ranges)
	{

		$sth = $this -> databaseHandle -> prepare("delete from WantedValues where Channel='Temperature'");

		$sth -> execute();
		foreach ($ranges as $range)
		{
			$sth = $this -> databaseHandle -> prepare("insert into WantedValues
								(Channel,Timestamp,Value)
								Values('Temperature','" . $this->LocalDate($range[0]) . "'," . $range[1] . ")");

			$sth -> execute();
		}
	}

	public function GetRange()
	{

		$sth = $this -> databaseHandle -> prepare("select 
							max(unix_timestamp(Timestamp)) as max,
							min(unix_timestamp(DATE_SUB(Timestamp, INTERVAL 30 DAY))) as min
							from Measures order by Timestamp");

		$sth -> execute();

		/* Group values by the first column */
		return $sth -> fetch(PDO::FETCH_ASSOC);

	}

}
?>