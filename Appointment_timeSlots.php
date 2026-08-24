<?php
$serverName = "CS3-DEV.ICT.RU.AC.ZA";
$user = "G24R8054";
$password = "RigUle24";
$database = "group1";
 // connect to db

$conn = new mysqli($serverName, $user, $password, $database);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}



$date = str_replace('/', '-', $_GET['date']);
$counsellor = $_GET['counsellor'];




$query = "
SELECT time FROM time_slots
WHERE date = '$date'
AND Councillor = '$counsellor'
AND time NOT IN (
    SELECT time FROM appointment
    WHERE date = '$date'
    AND Councillor = '$counsellor'
)"; // get unbooked slots

$result = $conn -> query($query); //php --> sql -->mysql
$time_slots = []; 

while($row = $result -> fetch_assoc()){ // fetch_assoc row at a time from query result 
    $time_slots[] = $row['time'];

     // return results to JS
}
    echo json_encode($time_slots);
    echo $query;



// used external sources to complete assignment
?>

