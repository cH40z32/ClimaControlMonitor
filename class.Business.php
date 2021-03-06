<?php
require_once ("class.DataAccess.php");
class Business extends DataAccess {
	function __construct($servername, $database, $username, $password) {
		parent::__construct($servername, $database, $username, $password);
	}

	public function GetMeasuresJSON($from, $to) {
		$result = $this -> GetMeasures($from, $to);

		$json = '{"cols":[  
			   {  
				  "label":"Time",
				  "type":"datetime"
				  
			   },
			   {  
				  "label":"Temperature",
				  "type":"number"
			   },
			   {  
				  "label":"Wanted Temperature",
				  "type":"number"
			   },
			   {  
				  "label":"Humidity",
				  "type":"number"
			   },
			   {  
				  "label":"Wanted Humidity",
				  "type":"number"
			   },
			   {  
				  "label":"IsVentilating",
				  "type":"number"
			   },
			   {  
				  "label":"IsIlluminating",
				  "type":"number"
			   },
			   {  
				  "label":"IsChannelAActive",
				  "type":"number"
			   },
			   {  
				  "label":"IsChannelBActive",
				  "type":"number"
			   }
			],
			"rows":[';

		//				  "role":"interval"
		//
		$distance = 250;
		foreach ($result as $value) {
			$json .= '{"c":[  
					 {  
						"v":"Date(' . $value["Timestamp"] . ')"
					 },
					 {  
						"v":' . $value["Temperature"] . '
					 },
					 {  
						"v":' . $value["WantedTemperature"] . '
					 },
					 {  
						"v":' . $value["Humidity"] . '
					 },
					 {  
						"v":' . $value["WantedHumidity"] . '
					 },
					 {  
						"v":' . (($value["IsVentilating"] == 1) ? $value["IsVentilating"] - 0 - $distance : 'null') . '
					 },
					 {  
						"v":' . (($value["IsIlluminating"] == 1) ? $value["IsVentilating"] - 10 - $distance : 'null') . '
					 },
					 {  
						"v":' . (($value["IsChannelAActive"] == 1) ? $value["IsVentilating"] - 20 - $distance : 'null') . '
					 },
					 {  
						"v":' . (($value["IsChannelBActive"] == 1) ? $value["IsVentilating"] - 30 - $distance : 'null') . '
					 }
				  ]},';

		}
		$json = substr($json, 0, strlen($json) - 1);
		$json .= ' ]
				}';

		return $this -> JSONEncode(json_decode($json));

	}

	public function GetLastMeasures() {
		$this -> JSONEncode($this -> GetLastMeasure());
	}

	public function GetWantedValuesJSON() {
		$this -> JSONEncode($this -> GetWantedValues());
	}

	protected function JSONEncode($string) {
		if ($string == null)
			return "";
		return json_encode($string, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES | JSON_NUMERIC_CHECK);
	}

	public function GetSettingsJSON() {
		$result = array();
		$result['TimeSpans'] = array();

		foreach ($this -> GetTimeSpans() as $value) {

			$start = strtotime($value['Start']);
			$end = strtotime($value['End']);
			$result['TimeSpans'][] = array($value['Channel'], $start, $end);
		}
		$result['WantedHumidity'] = array();
		foreach ($this -> GetWantedHumidity() as $value) {
			$time = strtotime($value['Timestamp']);
			$result['WantedHumidity'][] = array($time, $value['Value']);
		}
		$result['WantedTemperature'] = array();
		foreach ($this -> GetWantedTemperature() as $value) {
			$time = strtotime($value['Timestamp']);
			$result['WantedTemperature'][] = array($time, $value['Value']);
		}
		return $this -> JSONEncode($result);
	}

	public function GetLastMeasureJSON($lasttimestamp = null) {
		return $this -> JSONEncode($this -> GetLastMeasure($lasttimestamp));
	}

	public function GetCurrentTimeJSON() {
		$response = array();

		$response['day'] = date("j");
		$response['month'] = date("n");
		$response['year'] = date("Y");

		$response['hour'] = date("H");
		$response['minute'] = date("i");
		$response['second'] = date("s");
		return $this -> JSONEncode($response);
	}

}
?>