<?php

#Set timezone
@date_default_timezone_set('GMT');

ini_set('memory_limit', '512M');
ini_set('max_execution_time', '300');
ini_set('auto_detect_line_endings', TRUE);

/*
This function retrieves and returns the values for each
line from the data file. It retrieves each value by tallying
the number of semi-colons to ensure it is at the correct
position. Each character is then appended to a string until
another semi-colon is found.
*/
function getValue($line, $value, $pos)
{
    $lineLength = strlen($line);
    $inc = 0;
    for ($i = 0; $i < $lineLength; $i++) {
        if ($line[$i] == ';') {
            $inc += 1;
        }
        if ($inc == $pos) {
            $newInc = 1;
            if ($line[$i + $newInc] != ';') {
                $value .= $line[$i + $newInc];
                $newInc += 1;
            } else {
                return $value;
            }
        }
    }
}

#Array containing CSV header values
$header = 'siteID,ts,nox,no2,no,pm10,nvpm10,vpm10,nvpm2.5,pm2.5,vpm2.5,co,o3,so2,loc,lat,long';

#Array containing station IDs for file creation
$files = array(
    '188' => $header, '203' => $header, '206' => $header, '209' => $header, '213' => $header, '215' => $header,
    '228' => $header, '270' => $header, '271' => $header, '375' => $header, '395' => $header, '452' => $header, '447' => $header,
    '459' => $header, '463' => $header, '481' => $header, '500' => $header, '501' => $header
);

#Open main data file
#$dataFile = fopen('fragment.csv', 'r');
$dataFile = fopen('air-quality-data-2004-2019.csv', 'r');

#Retrieve each line from the data file, starting from the second line
fgets($dataFile);
while (($line = fgets($dataFile)) !== FALSE) {
    $isValid = FALSE;
    $inc = 0;
    $lineLength = strlen($line);
    #Iterate over each character within the line
    for ($i = 0; $i < $lineLength; $i++) {
        #Tally no. of semi-colons to allow for iteration over values
        if ($line[$i] == ';') {
            $inc += 1;
        }
        #If the following character is not a semi-colon then line contains NOx value
        if ($inc == 1) {
            if ($line[$i + 1] != ';') {
                $isValid = TRUE;
                break;
            }
        }
    }

    if ($isValid == FALSE) {
        $inc = 0;
        for ($j = 0; $j < $lineLength; $j++) {
            #Tally no. of semi-colons to allow for iteration over values
            if ($line[$j] == ';') {
                $inc += 1;
            }
            #If the following character is not a semi-colon then line contains CO value
            if ($inc == 11) {
                if ($line[$j + 1] != ';') {
                    $isValid = TRUE;
                    break;
                }
            }
        }
    }

    #If the line contains the necessary data then proceed
    if ($isValid == TRUE) {

        $siteID = '';
        $ts = '';
        $nox = '';
        $no2 = '';
        $no = '';
        $pm10 = '';
        $nvpm10 = '';
        $vpm10 = '';
        $nvpm25 = '';
        $pm25 = '';
        $vpm25 = '';
        $co = '';
        $o3 = '';
        $so2 = '';
        $loc = '';
        $geoPoint = '';

        #Get value for timestamp and covert to Unix timestamp
        for ($i = 0; $i < $lineLength; $i++) {
            if ($line[$i] != ';') {
                $ts .= $line[$i];
            } else {
                $ts = strtotime($ts);
                break;
            }
        }

        #For each required value, call the function to receive a returned value
        $siteID = getValue($line, $siteID, 4);
        $nox = getValue($line, $nox, 1);
        $no2 = getValue($line, $no2, 2);
        $no = getValue($line, $no, 3);
        $pm10 = getValue($line, $pm10, 5);
        $nvpm10 = getValue($line, $nvpm10, 6);
        $vpm10 = getValue($line, $vpm10, 7);
        $nvpm25 = getValue($line, $nvpm25, 8);
        $pm25 = getValue($line, $pm25, 9);
        $vpm25 = getValue($line, $vpm25, 10);
        $co = getValue($line, $co, 11);
        $o3 = getValue($line, $o3, 12);
        $so2 = getValue($line, $so2, 13);
        $loc = getValue($line, $loc, 17);
        $geoPoint = getValue($line, $geoPoint, 18);

        #Create a string containing all the necessary values
        $files[$siteID] .= "\n" . $siteID . ',' . $ts . ',' . $nox . ',' . $no2 . ',' . $no
            . ',' . $pm10 . ',' . $nvpm10 . ',' . $vpm10 . ',' . $nvpm25 . ',' . $pm25 . ',' . $vpm25 .
            ',' . $co . ',' . $o3 . ',' . $so2 . ',' . $loc . ',' . $geoPoint;
    }
}

$fileName = "";

#Append each string to the relevant data file
foreach ($files as $key => $file) {
    $fileName = 'data_' . $key . '.csv';
    $stationFile = fopen($fileName, 'a');
    fputs($stationFile, $file);
}