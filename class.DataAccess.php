<?php
require_once ("class.Database.php");
class DataAccess extends Database
{
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

	public function GetLastMeasure()
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
		return $sth -> fetch(PDO::FETCH_ASSOC);
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

	public function GetWantedValues()
	{

		$sth = $this -> databaseHandle -> prepare("
							select 
							Channel,
							Timestamp,
							Value
							from WantedValues 
							order by Channel desc,Timestamp desc");

		$sth -> execute();
		/* Group values by the first column */
		return $sth -> fetchAll(PDO::FETCH_ASSOC);

	}

	public function SetTimeSpans($ranges)
	{
		$this -> clear("TimeSpans");
		foreach ($ranges as $range)
		{
			$sth = $this -> databaseHandle -> prepare("insert into TimeSpans 
							(Channel,Start,End)
							Values('" . $range[0] . "','" . $range[1] . "','" . $range[2] . "')");

			$sth -> execute();
		}
	}

	public function GetRange()
	{

		$sth = $this -> databaseHandle -> prepare("select 
							max(unix_timestamp(Timestamp)) as max,
							min(unix_timestamp(Timestamp)) as min
							from Measures order by Timestamp");

		$sth -> execute();

		/* Group values by the first column */
		return $sth -> fetch(PDO::FETCH_ASSOC);

	}

}
?>