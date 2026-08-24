<?php
$conn = new mysqli(
    "cs3-dev.ict.ru.ac.za",
    "G24R8054",
    "RigUle24",
    "group1"
); //Connect to database

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$studentNumber = $_POST['studentNumber'];
$name = $_POST['fullname'];
$surname = $_POST['surname'];
$date = $_POST['date'];
$councillor = $_POST['counsellor'];
$time = $_POST['slot_time'];
$notes = $_POST['notes'];

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Make sure form was submitted
if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    // Get form data
    $studentNumber = $_POST['studentNumber'];
    $name = $_POST['fullname'];
    $surname = $_POST['surname'];
    $date = $_POST['date'];
    $councillor = $_POST['counsellor'];
    $time = $_POST['slot_time'];
    $notes = $_POST['notes'];
    //$consent = isset($_POST['consent']) ? 1 : 0;

    // Prepare INSERT statement for appointment_new table
    $sql = "INSERT INTO appointment_new
        (studentNumber, fullname, surname, date, councillor, time, reason , status)
        VALUES ($studentNumber, $name , $surname, $date, $councillor ,$time  , $notes,  )";

    $stmt = $conn->prepare($sql);

    if (!$stmt) {
        die("Prepare failed: " . $conn->error);
    }

    // Bind the variables
    $stmt->bind_param(
        "sssssssi",
        $studentNumber,
        $name,
        $surname,
        $date,
        $councillor,
        $time,
        $notes,
        $consent
    );

    // Execute the statement
    if ($stmt->execute()) {
        // Redirect or show success
        header("Location: appointment.html?registered=1");
        exit();
    } else {
        echo "Could not submit appointment: " . $stmt->error;
    }

    $stmt->close();
}

$conn->close();
/*$appointment_info = "
    INSERT INTO appointment
    (StudentNumber, Name, Surname, date, Councillor, time, reason)
    VALUES('$studentNumber', '$name', '$surname', '$date', '$counsellor', '$time', '$notes')";

    $conn->query($appointment_info);

    echo "appointment booked!";*/

?>
    