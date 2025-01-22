<?php
header('Content-Type: application/json');
include('config.php');

if ($_SERVER['REQUEST_METHOD'] == 'GET' && isset($_GET['id'])) {
    $id = $db->real_escape_string($_GET['id']);
    $query = "
        SELECT calendar.*, comments.comment, comments.user AS user, comments.time AS comment_time, appointments.date, appointments.time, appointments.description
        FROM calendar
        LEFT JOIN comments ON calendar.id = comments.calendar_id
        LEFT JOIN appointments ON calendar.id = appointments.client_id
        WHERE calendar.id = '$id'
    ";
    $result = $db->query($query);

    if ($result->num_rows > 0) {
        $baseData = null;
        $comments = [];
        while ($row = $result->fetch_assoc()) {
            if (!$baseData) {
                // Set the base data from the first row.
                $baseData = [
                    "id" => $row["id"],
                    "client_name" => $row["client_name"],
                    "address" => $row["address"],
                    "email" => $row["email"],
                    "city" => $row["city"],
                    "zip_code" => $row["zip_code"],
                    "phone" => $row["phone"],
                    "date" => $row["date"],
                    "time" => $row["time"],
                    "description" => $row["description"],
                    "RDV_date" => $row["RDV_date"],
                    "RDV_time" => $row["RDV_time"],
                    "status" => $row["status"],
                ];
            }
            if (!is_null($row['comment'])) {
                $comments[] = [
                    "comment" => $row['comment'],
                    "user" => $row["user"],
                    "comment_time" => $row["comment_time"],
                ];
            }
        }

        // Format comments as key-value pairs like "comment1", "comment2", etc.
        $formattedComments = [];
        foreach ($comments as $index => $comment) {
            $formattedComments["comment" . ($index + 1)] = $comment;
        }

        // Add the formatted comments to the base data.
        $baseData["comments"] = $formattedComments;

        echo json_encode(['success' => true, 'data' => $baseData]);
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
