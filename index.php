<?php
include('config.php');

// Handle POST requests for adding, updating, deleting or importing information
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['action'])) {
    $action = $_POST['action'];
    switch ($action) {
        case 'Add':
            $client_name = $db->real_escape_string($_POST['client_name']);
            $event_date = empty($_POST['event_date']) ? 'NULL' : "'" . $db->real_escape_string($_POST['event_date']) . "'";
            $start_time = empty($_POST['start_time']) ? 'NULL' : "'" . $db->real_escape_string($_POST['start_time']) . "'";
            $address = "'" . $db->real_escape_string($_POST['address']) . "'";
            $email = "'" . $db->real_escape_string($_POST['email']) . "'";
            $city = "'" . $db->real_escape_string($_POST['city']) . "'";
            $zip_code = "'" . $db->real_escape_string($_POST['zip_code']) . "'";
            $phone = "'" . $db->real_escape_string($_POST['phone']) . "'";
            $RDV_date = "'" . $db->real_escape_string($_POST['RDV_date']) . "'";
            $RDV_time = "'" . $db->real_escape_string($_POST['RDV_time']) . "'";
        
            // Build the query
            $insertQuery = "
                INSERT INTO calendar 
                (client_name, address, email, city, zip_code, phone, RDV_date, RDV_time)
                VALUES ('$client_name', $address, $email, $city, $zip_code, $phone, $RDV_date, $RDV_time)
            ";
            $db->query($insertQuery);
            
            $calendar_id = $db->insert_id;
            $appointmentsQuery = "
                INSERT INTO appointments (client_id, date, time)
                VALUES ($calendar_id, $event_date, $start_time)
            ";
            echo($appointmentsQuery);
            $db->query($appointmentsQuery);

            break;
            
        case 'Update':
            $id = $db->real_escape_string($_POST['id']);
            
            // Helper function to handle null values being passed
            function sanitizeInput($db, $value) {
                return empty($value) ? "NULL" : "'" . $db->real_escape_string($value) . "'";
            }
            
            $client_name = sanitizeInput($db, $_POST['client_name']);
            $event_date = sanitizeInput($db, $_POST['event_date']);
            $start_time = sanitizeInput($db, $_POST['start_time']);
            $address = sanitizeInput($db, $_POST['address']);
            $email = sanitizeInput($db, $_POST['email']);
            $city = sanitizeInput($db, $_POST['city']);
            $zip_code = sanitizeInput($db, $_POST['zip_code']);
            $phone = sanitizeInput($db, $_POST['phone']);
            $comment = sanitizeInput($db, $_POST['comments']);

            $updateQueryCalendar = "UPDATE calendar 
                            SET client_name=$client_name, 
                                address=$address, 
                                email=$email, 
                                city=$city, 
                                zip_code=$zip_code, 
                                phone=$phone 
                            WHERE id='$id'";
            $db->query($updateQueryCalendar);
            $updateQueryComments = "INSERT INTO comments (calendar_id, comment, user)
                                    VALUES ('$id', $comment, 'derek')";
            $db->query($updateQueryComments);

            if ($start_time != 'NULL' && $event_date != 'NULL') {
                $deleteQueryAppointments = "DELETE FROM appointments WHERE client_id = $id";
                $db->query($deleteQueryAppointments);
            
                $updateQueryAppointments = "INSERT INTO appointments (client_id, date, time)
                VALUES ($id, $event_date, $start_time)";
                $db->query($updateQueryAppointments);

                $updateQueryStatus = "
                    UPDATE calendar 
                    SET status = 
                        CASE 
                            WHEN CONCAT($event_date, ' ', $start_time) < NOW() THEN 'completed'
                            ELSE 'assigned'
                        END
                    WHERE id = $id";
                $db->query($updateQueryStatus);
            
            }
            else{
                $updateQueryStatus = "UPDATE calendar SET status = 'pending' WHERE id = $id";
                $db->query($updateQueryStatus);
            }
            break;            

        case 'Delete':
            $id = $db->real_escape_string($_POST['id']);
            $deleteQuery = "DELETE FROM calendar WHERE id='$id'";
            $db->query($deleteQuery);
            break;
        case 'import':
            if (isset($_FILES['file']) && $_FILES['file']['error'] === UPLOAD_ERR_OK) {
                $fileTmpPath = $_FILES['file']['tmp_name'];
                $fileName = $_FILES['file']['name'];
        
                // Ensure the file is a CSV file (add the possibility of different file types in future)
                $fileExtension = pathinfo($fileName, PATHINFO_EXTENSION);
                if (strtolower($fileExtension) !== 'csv') {
                    echo "Error: Only CSV files are allowed.";
                    exit();
                }
        
                $fileHandle = fopen($fileTmpPath, 'r');
                if ($fileHandle === false) {
                    echo "Error: Could not open the file.";
                    exit();
                }
        
                // Parse each row in the CSV
                while (($row = fgetcsv($fileHandle, 1000, ',', '"', '\\')) !== false) {
                    if (count($row) < 10) continue;
        
                    // Extract fields
                    $firstName = $db->real_escape_string($row[0]);
                    $lastName = $db->real_escape_string($row[1]);
                    $address = $db->real_escape_string($row[2]);
                    $city = $db->real_escape_string($row[3]);
                    $zipCode = $db->real_escape_string($row[4]);
                    $phone = $db->real_escape_string($row[5]);
                    $email = $db->real_escape_string($row[6]);
                    $rdvDate = date('Y-m-d', strtotime($row[7]));
                    $rdvTime = $db->real_escape_string($row[8]);
        
                    $insertQuery = "
                        INSERT INTO calendar (client_name, event_date, start_time, address, email, city, zip_code, phone, RDV_date, RDV_time)
                        VALUES ('$firstName $lastName', NULL, NULL, '$address', '$email', '$city', '$zipCode', '$phone', '$rdvDate', '$rdvTime')
                    ";
                    $db->query($insertQuery);
                }
        
                fclose($fileHandle);
                echo "Import successful!";
            } else {
                echo "Error: File upload failed.";
            }
            break;
    }
    header("Location: " . $_SERVER['PHP_SELF']); // Redirect to avoid resubmission
    exit();
}

// Fetch data from database
$query = "SELECT * FROM calendar ORDER BY client_name ASC";
$result = $db->query($query);
?>

<?php include('header1.php'); ?>

<body>
<div id="navbar">
    <nav class="navbar navbar-expand-lg navbar-dark bg-dark">
        <a href="index.php" class="navbar-brand mr-4">Home</a> 
        <a href="calendar.php" class="navbar-brand mr-4">Calendar</a> 
        <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarNavDropdown" aria-controls="navbarNavDropdown" aria-expanded="false" aria-label="Toggle navigation">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="navbarNavDropdown">
            <form class="form-inline ml-auto"> 
                <div class="input-group mr-2">
                    <input type="text" id="table_search" class="form-control" placeholder="Search...">
                </div>
                <button type="button" class="btn btn-info" data-toggle="modal" data-target="#addModal">Add New Entry</button>
                <button type="button" class="btn btn-info" data-toggle="modal" data-target="#importModal">Import Clients</button>
            </form>
        </div>
    </nav>
</div>
<div class="container-fluid">

    <br>
    <div class="table-responsive">
        <table id="info_table" class="table table-bordered table-striped">
            <thead class="thead">
                <tr>
                    <th class="apply">Client Name</th>
                    <th class="apply">City</th>
                    <th>Address</th>
                    <th>Phone Number</th>
                    <th class='apply'> Appointment Status</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php
				
				$counter = 1; // Initialize counter for incremental IDs
                if ($result->num_rows > 0) {
                    while ($row = $result->fetch_assoc()) {
                        echo "<tr>";
                        echo "<td>" . htmlspecialchars($row['client_name']) . "</td>";
                        echo "<td>" . htmlspecialchars($row['city'] ?? '') . "</td>";
                        echo "<td>" . htmlspecialchars($row['address']) . "</td>";
                        echo "<td>" . htmlspecialchars($row['phone']) . "</td>";
                        if($row['status'] == 'completed'){echo "<td style='color: green; font-weight: bold;'>" . htmlspecialchars("Completed!") . "</td>";}
                        elseif($row['status'] == 'pending'){echo "<td style='color: grey; font-weight: bold;'>" . htmlspecialchars("Pending") . "</td>";}
                        elseif($row['status'] == 'assigned'){echo "<td style='color: blue; font-weight: bold;'>" . htmlspecialchars("Assigned") . "</td>";}
                        else{echo "<td <td style='color: red; font-weight: bold;'>" . htmlspecialchars("SOMETHING WRONG") . "</td>";}
                        echo "<td>
                            <button class='btn btn-danger action-btn view-btn' data-id='" . $row['id'] . "' data-toggle='modal' data-target='#viewModal'>View</button>
                            <button class='btn btn-primary action-btn edit-btn' 
                                data-id='" . $row['id'] . "' 
                                data-client_name='" . htmlspecialchars($row['client_name']) . "' 
                                data-event_date='" . htmlspecialchars($row['event_date'] ?? '') . "' 
                                data-start_time='" . htmlspecialchars($row['start_time'] ?? '') . "' 
                                data-address='" . htmlspecialchars($row['address']) . "' 
                                data-email='" . htmlspecialchars($row['email'] ?? '') . "' 
                                data-city='" . htmlspecialchars($row['city'] ?? '') . "' 
                                data-zip_code='" . htmlspecialchars($row['zip_code'] ?? '') . "' 
                                data-phone='" . htmlspecialchars($row['phone']) . "' 
                                data-toggle='modal' 
                                data-target='#editModal'>Edit</button>
                            <button class='btn btn-danger action-btn delete-btn' data-id='" . $row['id'] . "' data-toggle='modal' data-target='#deleteModal'>Delete</button>
                        </td>";
                        echo "</tr>";
						$counter++;
                    }
                } else {
                    echo "<tr><td colspan='5'>No data found</td></tr>";
                }
				  
                ?>
            </tbody>
        </table>
    </div>

    <!-- Add Modal -->
    <div class="modal fade" id="addModal" tabindex="-1" role="dialog" aria-labelledby="addModalLabel" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="addModalLabel">Add New Entry</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <form method="post">
                    <div class="modal-body">
                        <div class="form-group">
                            <label style="color:red;">Client Name*</label>
                            <input type="text" name="client_name" class="form-control" required>
                        </div>
                        <div class="form-group">
                            <label style="color:red;">Phone Number*</label>
                            <input type="text" name="phone" class="form-control" required>
                        </div>
                        <div class="form-group">
                            <label style="color:red;">Address*</label>
                            <input type="text" name="address" class="form-control" required>
                        </div>
                        <div class="form-group">
                            <label>Date</label>
                            <input type="date" name="event_date" class="form-control">
                        </div>
                        <div class="form-group">
                            <label>Start Time</label>
                            <input type="time" name="start_time" class="form-control" step="3600">
                        </div>
                        <div class="form-group">
                            <label>Email</label>
                            <input type="text" name="email" class="form-control">
                        </div>
                        <div class="form-group">
                            <label>City</label>
                            <input type="text" name="city" class="form-control">
                        </div>
                        <div class="form-group">
                            <label>Zipcode</label>
                            <input type="text" name="zip_code" class="form-control">
                        </div>
                        <input type="hidden" name="RDV_date" id="current_date">
                        <input type="hidden" name="RDV_time" id="current_time">
                    </div>
                    <div class="modal-footer">
                        <button type="submit" name="action" value="Add" class="btn btn-primary">Add</button>
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Edit Modal -->
    <div class="modal fade" id="editModal" tabindex="-1" role="dialog" aria-labelledby="editModalLabel" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="editModalLabel">Edit Entry</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <form method="post">
                    <div class="modal-body">
                        <input type="hidden" name="id" id="edit-id">
                        <div class="form-group">
                            <label>Client Name</label>
                            <input type="text" name="client_name" id="edit-client_name" class="form-control" required>
                        </div>
                        <div class="form-group">
                            <label>Date</label>
                            <input type="date" name="event_date" id="edit-event_date" class="form-control">
                        </div>
                        <div class="form-group">
                            <label>Start Time</label>
                            <input type="time" name="start_time" id="edit-start_time" class="form-control">
                        </div>
                        <div class="form-group">
                            <label>Address</label>
                            <input type="text" name="address" id="edit-address" class="form-control" required>
                        </div>
                        <div class="form-group">
                            <label>Email</label>
                            <input type="text" name="email" id="edit-email" class="form-control">
                        </div>
                        <div class="form-group">
                            <label>City</label>
                            <input type="text" name="city" id="edit-city" class="form-control">
                        </div>
                        <div class="form-group">
                            <label>Zipcode</label>
                            <input type="text" name="zip_code" id="edit-zip_code" class="form-control">
                        </div>
                        <div class="form-group">
                            <label>Phone Number</label>
                            <input type="text" name="phone" id="edit-phone" class="form-control" required>
                        </div>
                        <div class="form-group">
                            <label>Comments</label>
                            <textarea id="edit-comments" class="form-control" name="comments" rows="4" cols="50" placeholder="Write your comments here..."></textarea>
                        </div>
                        <input type="hidden" name="RDV_date">
                        <input type="hidden" name="RDV_time">
                    </div>
                    <div class="modal-footer">
                        <button type="submit" name="action" value="Update" class="btn btn-primary btn-info">Save Changes</button>
                        <button type="button" class="btn btn-secondary btn-info" data-dismiss="modal">Close</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Delete Modal -->
    <div class="modal fade" id="deleteModal" tabindex="-1" role="dialog" aria-labelledby="deleteModalLabel" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="deleteModalLabel">Confirm Deletion</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <form method="post">
                    <div class="modal-body">
                        Are you sure you want to delete this entry?
                        <input type="hidden" name="id" id="delete-id">
                    </div>
                    <div class="modal-footer">
                        <button type="submit" name="action" value="Delete" class="btn btn-danger">Delete</button>
                        <button type="button" class="btn btn-secondary btn-info" data-dismiss="modal">Cancel</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Import Modal -->
    <div class="modal fade" id="importModal" tabindex="-1" role="dialog" aria-labelledby="importModalLabel" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="importModalLabel">Import a list of clients</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <form method="post" enctype="multipart/form-data">
                    <div class="modal-body">
                        <div class="form-group">
                            <label>CSV file</label>
                            <input type="file" name="file" class="form-control" required>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="submit" name="action" value="import" class="btn btn-primary">Add</button>
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                    </div>
                </form>
            </div>
        </div>
    </div> 
    
    <!--View modal-->
    <div class="modal fade" id="viewModal" tabindex="-1" role="dialog" aria-labelledby="viewModalLabel" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="viewModalLabel">View Entry Details</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <p><strong>Client Name:</strong> <span id="view-client_name"></span></p>
                    <p><strong>Quote Date:</strong> <span id="view-event_date"></span> <span id="view-start_time"></span></p>
                    <p><strong>Address:</strong> <span id="view-address"></span></p>
                    <p><strong>City:</strong> <span id="view-city"></span></p>
                    <p><strong>Zip Code:</strong> <span id="view-zip_code"></span></p>
                    <p><strong>Phone Number:</strong> <span id="view-phone"></span></p>
                    <p><strong>Email:</strong> <span id="view-email"></span></p>
                    <p><strong>RDV Date:</strong> <span id="view-RDV_date"></span>, <span id="view-RDV_time"></span></p>
                    <p><strong>Comments:</strong></p>
                    <div id="comments-box">
                        <span id="view-comments"></span>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener("DOMContentLoaded", function () {
    const currentDate = new Date();
    const date = currentDate.toISOString().split("T")[0];
    const time = currentDate.toTimeString().split(" ")[0].slice(0, 5);
    document.getElementById("current_date").value = date;
    document.getElementById("current_time").value = time;
});

$(document).ready(function () {
    $(".view-btn").on("click", function () {
        const id = $(this).data("id");

        $.ajax({
            url: "api.php",
            method: "GET",
            data: { id: id }, 
            dataType: "json", 
            success: function (response) {
                if (response.success) {
                    $("#view-client_name").text(response.data.client_name);
                    
                    if(response.data.date && response.data.time){
                        $("#view-event_date").text(response.data.date + ",");
                        $("#view-start_time").text(response.data.time);
                    }
                    else{
                        $("#view-event_date").text("Not yet appointed");
                        $("#view-start_time").text(" ");
                    }    
                    
                    $("#view-comments").text(response.data.comment);
                    $("#view-address").text(response.data.address);
                    $("#view-city").text(response.data.city);
                    $("#view-zip_code").text(response.data.zip_code);
                    $("#view-phone").text(response.data.phone);
                    $("#view-email").text(response.data.email);
                    $("#view-RDV_date").text(response.data.RDV_date);
                    $("#view-RDV_time").text(response.data.RDV_time);
                } else {
                    alert("Failed to fetch data. Please try again.");
                }
            },
        });
    });
});

$(document).ready(function() {
    // Initialize DataTable without the default search box
    var table = $('#info_table').DataTable({
        dom: 'lrtip', // This removes the default search box
        pageLength: 25, 
		order: [[1, 'asc']], 
        columnDefs: [
            { orderable: false, targets: -1}, 
        ]
    });

    // Custom search functionality
    $('#table_search').on('keyup', function() {
        table.search(this.value).draw();
    });

    // edit button handler
    $('#info_table').on('click', '.edit-btn', function() {
        var id = $(this).data('id');
        var client_name = $(this).data('client_name');
        var event_date = $(this).data('event_date');
        var start_time = $(this).data('start_time');
        var address = $(this).data('address');
        var email = $(this).data('email');
        var city = $(this).data('city');
        var zip_code = $(this).data('zip_code');
        var phone = $(this).data('phone');
        
        $('#edit-id').val(id);
        $('#edit-client_name').val(client_name);
        $('#edit-event_date').val(event_date);
        $('#edit-start_time').val(start_time);
        $('#edit-address').val(address);
        $('#edit-email').val(email);
        $('#edit-city').val(city);
        $('#edit-zip_code').val(zip_code);
        $('#edit-phone').val(phone);
    });

    // Use event delegation for the delete button click handler
    $('#info_table').on('click', '.delete-btn', function() {
        var id = $(this).data('id');
        $('#delete-id').val(id);
    });
});
</script>
</body>
</html>