<?php
//header("Content-Type: application/json");
error_reporting(E_ALL);
ini_set("display_errors", true);
require_once ('class.Business.php');

$servername = "db10.sysproserver.de";
$username = "clima";
$password = "YeHiepYerc!osDet";
$database = "ClimaControlMonitor";

$database = new Business($servername, $database, $username, $password);

switch ($_REQUEST['action']) {
	case 'GetRange' :
		echo json_encode($database -> GetRange());
		break;

	case 'GetLineChartData' :
		$from = $_REQUEST['from'];
		$to = isset($_REQUEST['to']) ? $_REQUEST['to'] : date();
		print($database -> GetMeasuresJSON($from, $to));
		break;

	case 'GetSettings' :
		print($database -> GetSettingsJSON());
		break;

	case 'SetSettings' :
		$decoded = json_decode($_REQUEST['data']);
		$database -> SetTimeSpans($decoded);
		break;

	case 'GetTime' :
		print($database -> GetCurrentTimeJSON());
		break;

	case 'GetLastMeasure' :
		$lastTimestamp = isset($_REQUEST['LastTimestamp']) ? $_REQUEST['LastTimestamp'] : null;
		print($database -> GetLastMeasureJSON($lastTimestamp));
		break;

	case 'SetWantedHumidity' :
		$decoded = json_decode($_REQUEST['data']);
		print($database -> SetWantedHumidity($decoded));
		break;

	case 'SetWantedTemperature' :
		$decoded = json_decode($_REQUEST['data']);
		print($database -> SetWantedTemperature($decoded));
		break;

	case 'AddMeasure' :
		if (isset($_REQUEST['Temperature']) && isset($_REQUEST['WantedTemperature']) && isset($_REQUEST['Humidity']) && isset($_REQUEST['WantedHumidity']) && isset($_REQUEST['IsVantillating']) && isset($_REQUEST['IsIlluminating']) && isset($_REQUEST['IsChannelAActive']) && isset($_REQUEST['IsChannelBActive'])) {
			$database -> AddMeasure(doubleval($_REQUEST['Temperature']), doubleval($_REQUEST['Humidity']), doubleval($_REQUEST['WantedTemperature']), doubleval($_REQUEST['WantedHumidity']), intval($_REQUEST['IsVantillating']), intval($_REQUEST['IsIlluminating']), intval($_REQUEST['IsChannelAActive']), intval($_REQUEST['IsChannelBActive']));
			echo "Success";
		}
		break;

	default :
		break;
}
?>
