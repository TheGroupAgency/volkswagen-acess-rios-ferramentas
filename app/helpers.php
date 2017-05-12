<?php
/**
 * Search for files by extension in specific directory
 * @param  string 			$searchDirectory 	directory path to search
 * @param  array|string 	$searchExtension 	extensions to match
 * @param  boolean 			$firstMatch     	if searching for one file or multiple files
 * @return string|array
 */
function searchFileByExtension($searchDirectory, $searchExtension, $firstMatch = true) {
	if (!is_dir($searchDirectory)) return null;

	$fileList = scandir($searchDirectory);
	if (empty($fileList)) return null;

	$result = array();

	foreach($fileList as $file) {
		if (is_array($searchExtension)) {
			$found = null;
			foreach($searchExtension as $extension) {
				if (pathinfo($file, PATHINFO_EXTENSION) == $extension) {
					$result[] = $file;
					if ($firstMatch) {
						$found = true;
						break;
					}
				}
			}

			if ($found && $firstMatch) {
				break;
			}
		} // end array extension search
		else {
			if (pathinfo($file, PATHINFO_EXTENSION) == $searchExtension) {
				$result[] = $file;
				if ($firstMatch) {
					break;
				}
			}
		} // end string extension search
	} // end foreach file
	
	if ($firstMatch) {
		return empty($result) ? null : $result[0];
	}
	else {
		return $result;
	}
}

function getAvailableFileName($originalFileName, $path) {
	$count = 1;
	$newFileName = $originalFileName;

	$pathInfo = pathinfo($originalFileName);
	
	while(File::exists($path . "/" . $newFileName)) {
		$newFileName = $pathInfo["filename"] . "_" . $count . "." . $pathInfo['extension'];
		$count++;
	}

	return $newFileName;
}

function transformYearStringInArray($str) {
	$years = array();
	if (strpos($str, "-") !== false) { // its a date range
		$yearParts = explode("-", $str);
		for ($i = $yearParts[0]; $i <= $yearParts[1]; $i++) {
			$years[] = $i;
		}
	}
	else if (strpos($str, ">") !== false) { // if year is "greather than"
		$initialYear = substr($str, 0, 4);

		for($i = $initialYear; $i < date("Y"); $i++) {
			$years[] = $i;
		}

	}
	else { // simple year
		$years[] = $str;
	}
	
	return $years;
}
?>