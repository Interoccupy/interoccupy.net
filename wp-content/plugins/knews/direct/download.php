<?php
global $Knews_plugin;

if ($Knews_plugin) {

	if (! $Knews_plugin->initialized) $Knews_plugin->init();
	
	$filename = $Knews_plugin->get_safe('file');
	
	if ($filename != '') {
		$filename = str_replace('/','*', $filename);
		$filename = str_replace('\\','*', $filename);
		$filename = str_replace('..','*', $filename);
		if ($file = @file_get_contents(KNEWS_DIR . '/tmp/' . $filename)) {

			if (strpos($filename,'.csv') !== false) {
				header('Content-type: text/csv');
				header('Content-disposition: attachment;filename=' . $filename);
			}
			if (strpos($filename,'.png') !== false) {
				header('Content-type: image/png');
			}
			echo $file;
			die();
		}
	}
}
header('HTTP/1.0 404 Not Found');
echo '<html><head><title>404 Not Found</title></head>';
echo "<body><h1>404 Not Found</h1>";
echo "The page that you have requested could not be found.</body>";

die();
?>
