<?php

#Create array containing station IDs
$files = array(
    '188', '203', '206', '209', '213', '215', '228', '270',
    '271', '375', '395', '447', '452', '459', '463', '481', '500', '501'
);

$id = "";
$name = "";
$geocode = "";

#Iterate over each station
foreach ($files as $file) {

    #Create an XML document for the station
    $xml = new XMLWriter();
    $xml->openMemory();
    $xml->startDocument('1.0', 'UTF-8');
    $xml->setIndent(true);

    #Open the existing CSV file for the station 
    $dataFile = fopen('data_' . $file . '.csv', 'r');

    #Get the attributes required for the root element
    for ($i = 0; $i < 2; $i++) {
        $details = fgetcsv($dataFile);
    }

    if (isset($details[0])) {
        $id = $details[0];
    }
    if (isset($details[14])) {
        $name = $details[14];
    }
    if (isset($details[15]) and isset($details[16])) {
        $geocode = $details[15] . "," . $details[16];
    }

    #Close the file as the file will need to be iterated again from the second line
    fclose($dataFile);

    #Create the child element and its required attributes
    $xml->startElement('station');
    $xml->writeAttribute('id', $id);
    $xml->writeAttribute('name', $name);
    $xml->writeAttribute('geocode', $geocode);

    #Reopen existing CSV file
    $dataFile = fopen('data_' . $file . '.csv', 'r');

    #Starting from second line of file begin iterating
    fgetcsv($dataFile);
    while (($line = fgetcsv($dataFile)) !== FALSE) {
        #Certain files require the removal of redundant data
        if (($file == '188') || ($file == '213') || $file == '452') {
            if ((!empty($line[2])) && (!empty($line[3])) && (!empty($line[4]))) {
                $xml->startElement('rec');
                $xml->writeAttribute('ts', $line[1]);
                $xml->writeAttribute('nox', $line[2]);
                $xml->writeAttribute('no', $line[4]);
                $xml->writeAttribute('no2', $line[3]);
                $xml->endElement();
            }
        } else {
            $xml->startElement('rec');
            $xml->writeAttribute('ts', $line[1]);
            $xml->writeAttribute('nox', $line[2]);
            $xml->writeAttribute('no', $line[4]);
            $xml->writeAttribute('no2', $line[3]);
            $xml->endElement();
        }
    }

    $xml->endElement();

    $xml->endDocument();

    #Output the contents into a newly created XML file, specifically for the station
    file_put_contents('data_' . $file . '.xml', $xml->outputMemory());

    #Ensure leading line doesn't exist for each file
    if ($file != '481') {
        file_put_contents('data_' . $file . '.xml', trim(file_get_contents('data_' . $file . '.xml')));
    }
}