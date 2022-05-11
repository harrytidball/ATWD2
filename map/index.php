<?php

include_once 'config.php';

#Create associative array of stations
$stationIDs = array(
    'AURN Bristol Centre' => 188, 'Brislington Depot' => 203, 'Rupert Street' => 206,
    'IKEA M32' => 209, 'Old Market' => 213, 'Parson Street School' => 215,
    'Temple Meads Station' => 228, 'Wells Road' => 270, 'Trailer Portway Park and Ride' => 271,
    'Newfoundland Road Police Station' => 375, "Shiner's Garage" => 395, 'AURN St Pauls' => 447,
    'Bath Road' => 452, 'Cheltenham Road \ Station Road' => 459, 'Fishponds Road' => 463,
    'CREATE Centre Roof' => 481, 'Temple Way' => 500, 'Colston Avenue' => 501
);

$names = array();
$geocodes = array();
$lats = array();
$lngs = array();

$nox = 0;
$noxFrequency = 0;
$noxAverage = 0;

$no = 0;
$noFrequency = 0;
$noAverage = 0;

$no2 = 0;
$no2Frequency = 0;
$no2Average = 0;

$startRange = "1420070400"; #01/01/2015
$endRange = "1577836799"; #31/12/2019
$dataUnavailable = "No data available";

#Get the name and geocode for each station, separating the geocode into lat and long values
foreach ($stationIDs as $station) {
    $xml = simplexml_load_file("../data_" . $station . ".xml");
    if ($station == 271) {
        array_push($names, "Trailer Portway Park and Ride");
    } else {
        array_push($names, (string)$xml['name']);
    }
    $geocodes = explode(",", (string)$xml['geocode']);
    array_push($lats, $geocodes[0]);
    array_push($lngs, $geocodes[1]);
}

#Verify if a station has been selected
if (isset($_GET['station'])) {
    $selectedStation = $_GET['station'];
    $selectedStationID = $stationIDs[$selectedStation];
    #Load the file for the selected station
    $xml = simplexml_load_file("../data_" . $selectedStationID . ".xml");
    #Get the pollutant values
    foreach ($xml->children() as $rec) {
        $timestamp = (string)$rec->attributes()->ts;
        #Only select records which are between the chosen start and end times
        if (($timestamp >= $startRange) && ($timestamp < $endRange)) {
            if ((float)$rec->attributes()->nox > 0) {
                $nox += (float)$rec->attributes()->nox;
                $noxFrequency += 1;
            }
            if ((float)$rec->attributes()->no2 > 0) {
                $no2 += (float)$rec->attributes()->no2;
                $no2Frequency += 1;
            }
            if ((float)$rec->attributes()->no > 0) {
                $no += (float)$rec->attributes()->no;
                $noFrequency += 1;
            }
        }
    }

    #Calculate the average pollutant value by dividing the total by the number of occurences
    if ($noxFrequency == 0) {
        $noxAverage = 0;
    } else {
        $noxAverage = $nox / $noxFrequency;
    }
    if ($no2Frequency == 0) {
        $no2Average = 0;
    } else {
        $no2Average = $no2 / $no2Frequency;
    }
    if ($noFrequency == 0) {
        $noAverage = 0;
    } else {
        $noAverage = $no / $noFrequency;
    }
}

?>

<!DOCTYPE html>
<html lang="en">

<head>
    <title>Mapping</title>
    <link href="map.css" rel="stylesheet">
    <script>
        var names = <?php echo json_encode($names); ?>;
        var lats = <?php echo json_encode($lats); ?>;
        var lngs = <?php echo json_encode($lngs); ?>;

        //If a station has been selected then set the default location to the station
        <?php if (isset($_GET['station'])) {
            $index = array_search($_GET['station'], $names);
        ?>
            var lat = lats[<?php echo $index; ?>];
            var lng = lngs[<?php echo $index; ?>];
            //Zoom in on the selected station
            var extraZoom = 5;
        <?php } else { ?>
            var extraZoom = 0;
            var lat = lats[13];
            var lng = lngs[13];
        <?php } ?>

        //Initialise and add the map
        function initMap() {
            const map = new google.maps.Map(document.getElementById("map"), {
                zoom: 11.5 + extraZoom,
                minZoom: 11.5,
                maxZoom: 20,
                center: {
                    lat: parseFloat(lat),
                    lng: parseFloat(lng)
                },
                disableDefaultUI: true,
                zoomControl: true,
            });

            var i;

            for (i = 0; i < lats.length; i++) {
                addMarker({
                    coordinates: {
                        lat: parseFloat(lats[i]),
                        lng: parseFloat(lngs[i])
                    }
                }, "<h3 style='infowindow'>" + names[i] + "</h3>", names[i]);
            }

            function addMarker(props, content, names) {
                var marker = new google.maps.Marker({
                    position: props.coordinates,
                    map: map,
                    icon: 'radar.png',
                    animation:google.maps.Animation.DROP
                })
                //Create new infowindow and add content
                var infowindow = new google.maps.InfoWindow({
                    content: content,
                });

                //Listener for mouseover marker, prompting infowindow to be displayed
                google.maps.event.addListener(marker, 'mouseover', function() {
                    infowindow.open(map, marker);
                    marker.setIcon('radar-red.png')
                });

                //Listener for mouseout marker, prompting infowindow to be hidden
                google.maps.event.addListener(marker, 'mouseout', function() {
                    infowindow.close(map, marker);
                    marker.setIcon('radar.png')
                });

                //Listener for marker being clicked
                google.maps.event.addListener(marker, 'click', function() {
                    //Display details of selected station
                    window.location.replace("index.php?station=" + names);
                });

            }

        }
    </script>
</head>

<body>
    <h2 class="title">Select a Station to View Data
    <p>
    <button class="centre" onclick="location.href='index.php';">Centre Map</button>
    </p>
    </h2>
    <div id="map"></div>
    <?php if (isset($_GET['station'])) { ?>
        <p>
        <h3 class="subtitle">Average readings for <span style="color:#333;"><b><?php echo $selectedStation; ?></b></span>
            between 1st January 2015 and 31st December 2019.</h3>
        </p>
        <p>
        <div id="table">
            <table>
                <tr>
                    <th>Pollutant</th>
                    <th>Description</th>
                    <th>Unit</th>
                    <th>Mean Value</th>
                </tr>
                <tr>
                    <td>NOx</td>
                    <td>Concentration of oxides of nitrogen</td>
                    <td>μg/m3</td>
                    <td><?php if ($noxAverage == 0) {
                            echo $dataUnavailable;
                        } else {
                            echo number_format($noxAverage, 2);
                        } ?></td>
                </tr>
                <tr>
                    <td>NO2</td>
                    <td>Concentration of nitrogen dioxide</td>
                    <td>μg/m3</td>
                    <td><?php if ($no2Average == 0) {
                            echo $dataUnavailable;
                        } else {
                            echo number_format($no2Average, 2);
                        } ?></td>
                </tr>
                <tr>
                    <td>NO</td>
                    <td>Concentration of nitric oxide</td>
                    <td>μg/m3</td>
                    <td><?php if ($noAverage == 0) {
                            echo $dataUnavailable;
                        } else {
                            echo number_format($noAverage, 2);
                        } ?></td>
                </tr>
            </table>
        </div>
        </p>
    <?php } ?>

    <script src="https://maps.googleapis.com/maps/api/js?key=<?php echo GMAP_API_KEY; ?>&callback=initMap&libraries=&v=weekly" async></script>
</body>

</html>