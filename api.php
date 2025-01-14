<?php
header('Content-Type: application/json');
include('config.php');

if ($_SERVER['REQUEST_METHOD'] == 'GET' && isset($_GET['id'])) {
    $id = $db->real_escape_string($_GET['id']);
    $query = "
        SELECT calendar.*, comments.comment, appointments.date, appointments.time
        FROM calendar
        LEFT JOIN comments ON calendar.id = comments.calendar_id
        LEFT JOIN appointments ON calendar.id = appointments.client_id
        WHERE calendar.id = '$id'
    ";
    $result = $db->query($query);

    if ($result->num_rows > 0) {
        $data = $result->fetch_assoc();
        echo json_encode(['success' => true, 'data' => $data]);
    } else {
        echo json_encode(['success' => false, 'message' => 'No record found.']);
    }
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'GET' && isset($_GET['date'])) {
    $date = $_GET['date'];
    
    $query = "SELECT appointments.time, calendar.client_name, appointments.description
              FROM appointments 
              JOIN calendar ON appointments.client_id = calendar.id 
              WHERE appointments.date = ?";
    $stmt = $db->prepare($query);

    $stmt->bind_param("s", $date);
    $stmt->execute();
    $result = $stmt->get_result();

    $appointments = [];
    while ($row = $result->fetch_assoc()) {
        $appointments[] = $row;
    }

    echo json_encode($appointments, JSON_FORCE_OBJECT);
    $stmt->close();
    exit();
}
?>
