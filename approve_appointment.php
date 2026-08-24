<?php
//require 'databaseConnection.php';
$conn = new mysqli(
    "cs3-dev.ict.ru.ac.za",
    "G23S3219",
    "SedKga23",
    "group1"
); //Connect to database

//Get ID  and sanitize it to prevent SQL Injection                  
$appointment_id = $conn->real_escape_string($_POST["id"]);

$sql = "UPDATE appointment SET status='approved' where appointmentID=$appointment_id";

if($conn->query($sql) === TRUE) {
    echo "Appointment approved and moved to 'Upcoming Appointments'";
}else{
    echo "Error in updating record". $conn->error;
}

?>