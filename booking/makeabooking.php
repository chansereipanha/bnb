<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
    <link rel="stylesheet" href="https://code.jquery.com/ui/1.14.1/themes/base/jquery-ui.css">
    <script src="https://code.jquery.com/jquery-3.7.1.js"></script>
    <script src="https://code.jquery.com/ui/1.14.1/jquery-ui.js"></script>
    <script>
        $(document).ready(function() {
            $.datepicker.setDefaults({
                dateFormat: 'yy-mm-dd'
            });

            $(function() {
                checkinDate = $( "#checkinDate" ).datepicker();
                checkoutDate = $( "#checkoutDate" ).datepicker();

                function getDate(element) {
                    var date;
                    try {
                        date = $.datepicker.parseDate(dateFormat, element.value);
                    } catch (error) {
                        date = null;
                    }
                    return date;}
            });
        });
    </script>
</head>
<body>
<?php
    include 'checksession.php';
        checkUser();


    include "../config.php";
    $DBC = mysqli_connect(DBHOST, DBUSER, DBPASSWORD, DBDATABASE);

    if (mysqli_connect_errno()){
        echo "Error: Unable to connect to MySql" . mysqli_connect_error();
        exit;
    }

    function cleanInput($data){
        return htmlspecialchars(stripslashes(trim($data)));
    }

    

    if (isset($_POST['submit']) and !empty($_POST['submit']) and ($_POST['submit'] == 'Book')){

        $room = cleanInput($_POST['room']);
        $customer = cleanInput($_POST['customer']);
        $checkinDate = $_POST['checkinDate'];
        $checkoutDate = $_POST['checkoutDate'];
        $contactNumber = cleanInput($_POST['contactNumber']);
        $bookingExtras = cleanInput($_POST['bookingExtras']);
        
        $error = 0;
        $msg ="Error:";
    
        $in = new DateTime($checkinDate);
        $out = new DateTime($checkoutDate);
    
        if( $in >= $out){
            $error++;
            $msg .= "Arrival date cannot be earlier or equal to departure date";
            $arr = '';
        }

        $checkQuery = "SELECT * FROM booking 
        WHERE roomID = ? 
        AND checkinDate <= ? 
        AND checkoutDate >= ?";
        $stmt = mysqli_prepare($DBC, $checkQuery);
        mysqli_stmt_bind_param($stmt, 'iss', $room, $checkoutDate, $checkinDate);
        mysqli_stmt_execute($stmt);
        $result = mysqli_stmt_get_result($stmt);

        if (mysqli_num_rows($result) > 0) {
        $error++;
        $msg .= " The selected room is already booked for the given date range.";
        }
        
        mysqli_stmt_close($stmt);
        
        if($error == 0){
            $query = "INSERT INTO booking (roomID, customerID, checkinDate, checkoutDate, contactNumber, bookingExtras) VALUES (?,?,?,?,?,?)";
            $stmt = mysqli_prepare($DBC, $query);
            mysqli_stmt_bind_param($stmt, 'iissss', $room, $customer, $checkinDate, $checkoutDate, $contactNumber, $bookingExtras);
            mysqli_stmt_execute($stmt);
            mysqli_stmt_close($stmt);

            echo "<h2>New bookings made</h2>";
        } else{
            echo "<h5>$msg</h5>" .PHP_EOL;
        }
    }

    $queryRoom = 'SELECT roomID, roomname, description, roomtype, beds FROM room ORDER BY roomID';
    $resultRoom = mysqli_query($DBC, $queryRoom);
    $rowcountRoom = mysqli_num_rows($resultRoom);

    $queryCustomer = 'SELECT customerID, firstname, lastname, email, password FROM customer ORDER BY customerID';
    $resultCustomer = mysqli_query($DBC, $queryCustomer);
    $rowcountCustomer = mysqli_num_rows($resultCustomer);
?>

<h1>Make a booking</h1>
    <h2>
        <a href="listbookings.php">[Return to the Bookings listing]</a>
        <a href="../index.php">[Return to the main page]</a>
    </h2>
    <div class="form_container">
        <form class="form" method="POST">
            <div>
                <label for="room">Room(name,type,beds):</label>
                <select name="room" id="room">
                        <?php 
                            if($rowcountRoom > 0){
                                while($row = mysqli_fetch_assoc($resultRoom)){
                                    $id = $row['roomID']; ?>

                                    <option value="<?php echo $row['roomID']; ?>">
                                        <?php echo $row['roomname'] . ', ' 
                                            . $row['roomtype'] .', ' 
                                            .$row['beds'];
                                        ?>
                                    </option>
                                <?php }
                            } else echo "<option>No room found</option>";
                            mysqli_free_result($resultRoom);
                        ?>
                    </select>
            </div>

            <br>

            <div>
                <label for="customer">Customers:</label>
                <select name="customer" id="customer" >
                    <?php
                    if ($rowcountCustomer > 0) {
                        while ($row = mysqli_fetch_assoc($resultCustomer)) {
                            $id = $row['customerID']; ?>

                            <option value="<?php echo $row['customerID']; ?>">
                                <?php echo $row['customerID'] . ' '
                                    . $row['firstname'] . ', '
                                    . $row['lastname'];
                                ?>
                            </option>
                    <?php }
                    } else echo "<option>No customer found</option>";
                    mysqli_free_result($resultCustomer);
                    ?>
                </select>
            </div>

            <p>
                <input type="hidden" name="id" value="<?php echo $id;?>" >
            </p>

            <p>
                <label for="checkinDate">Check-in Date:</label>
                <input type="text" id="checkinDate" name="checkinDate" required >
            </p>
            <p>
                <label for="checkoutDate">Check-out Date</label>
                <input type="text" id="checkoutDate" name="checkoutDate" required>
            </p>
            <p>
                <label for="contactNumber">Contact Number:</label>
                <input type="text" id="contactNumber" name="contactNumber" required 
                pattern="\(\d{3}\) \d{3} \d{4}" >
            </p>
            <p>
                <label for="bookingExtras">Extras:</label>
                <input type="text" id="bookingExtras" name="bookingExtras" >
            </p>

            <input type="submit" name="submit" value="Book" onclick="searchBookings()">
            <a href="listbookings.php">[Cancel]</a>
        </form>
    </div>  

    <hr>
    
    <h3>Search for room availability</h3>
    <div>
        <form action="searchForm" method="get" name="searching" id="searchForm">
            <label>Start Date:</label>
            <input type="text" name="fromDate" id="fromDate" placeholder="yyyy-mm-dd" required>

            <label>End Date:</label>
            <input type="text" name="toDate" id="toDate" placeholder="yyyy-mm-dd" required>

            <input type="submit" value="Search availability">
        </form>
    </div>

                    <br>

    <div>
        <table border="1px" id="room_available_table">
            <thead>
                <tr>
                    <th>Room#</th>
                    <th>Room name</th>
                    <th>Room type</th>
                    <th>Beds</th>
                </tr>
            </thead>
            <tbody id="result"></tbody>
        </table>
    </div>
</body>
<script>
    $(document).ready(function(){
        $( "#fromDate" ).datepicker({dateFormat:"yy-mm-dd"});
        $( "#toDate" ).datepicker({dateFormat:"yy-mm-dd"});
        
        $("#searchForm").submit(function(event) {
            event.preventDefault();
            var fromDate = $("#fromDate").val();
            var toDate = $("#toDate").val();
            
            if (fromDate > toDate) {
                alert("From date cannot be later than To date.");
                return false; 
            }

            searchBookings(); 
        });
    });

    function searchBookings(){
        var fromDate = $("#fromDate").val();
        var toDate = $("#toDate").val();

        $.ajax({
            url: "bookingsearch.php",
            method: "GET",
            data: {fromDate: fromDate, toDate: toDate},
            success: function(response) {
                $("#result").html(response);
            },

            error: function(xhr, status, error) {
                console.error("AJAX Error:", status, error);
            }
        });
    }

</script>
</html>