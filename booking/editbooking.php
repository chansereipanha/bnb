<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Booking</title>
    <?php
        include "../header.php";
    ?>
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

<?php
    include 'checksession.php';
        checkUser();

    include "menu.php";
        echo '<div id="site_content">';

    include "sidebar.php";


    include "../config.php";
    $DBC = mysqli_connect(DBHOST, DBUSER, DBPASSWORD, DBDATABASE);

    if (mysqli_connect_errno()){
        echo "Error: Unable to connect to MySql" . mysqli_connect_error();
        exit;
    }

    function cleanInput($data)
    {
        return htmlspecialchars(stripslashes(trim($data)));
    }

    if ($_SERVER["REQUEST_METHOD"] == "GET") {
        $id = $_GET['id'];
        if (empty($id) or !is_numeric($id)) {
            echo "<h2>Invalid Booking ID</h2>"; //simple error feedback
            exit;
        } 
    }

    

    if (isset($_POST['submit']) and !empty($_POST['submit']) and ($_POST['submit'] == 'Update')){

        $room = cleanInput($_POST['room']);
        $checkinDate = $_POST['checkinDate'];
        $checkoutDate = $_POST['checkoutDate'];
        $contactNumber = cleanInput($_POST['contactNumber']);
        $bookingExtras = cleanInput($_POST['bookingExtras']);
        $bookingReview = cleanInput($_POST['bookingReview']);
        $id = cleanInput($_POST['id']);

        
        $upd = "UPDATE booking 
        SET roomID=?, checkinDate=?, checkoutDate=?, contactNumber=?, bookingExtras=?, bookingReview=? 
        WHERE bookingID=?";
        $stmt = mysqli_prepare($DBC, $upd);
        mysqli_stmt_bind_param($stmt, 'isssssi', $room, $checkinDate, $checkoutDate, $contactNumber, $bookingExtras, $bookingReview, $id);
        mysqli_stmt_execute($stmt);
        mysqli_stmt_close($stmt);
        echo "<h2>Booking details updated</h2>";
    }
        $query = 'SELECT booking.bookingID, booking.checkinDate, booking.checkoutDate, booking.contactNumber, booking.bookingExtras, booking.bookingReview, room.roomID, room.roomname, room.roomtype, room.beds
        FROM booking
        INNER JOIN room ON booking.roomID = room.roomID
        WHERE bookingID=' .$id;
        
        $result = mysqli_query($DBC, $query);
        $rowcount = mysqli_num_rows($result);
    
?>
<body>
    <h1>Edit a booking</h1>
        <h2>
            <a href="listbookings.php">[Return to the Bookings listing]</a>
            <a href="../index.php">[Return to main page]</a>
        </h2>
        <div>
            <form action="editbooking.php" method="POST">
                <input type="hidden" name="id" value="<?php echo $id;?>">
                    <p>
                        <label for="room">Rooms:</label>
                        <select name="room" id="room">
                            <?php
                                if($rowcount > 0){
                                    $row = mysqli_fetch_assoc($result);
                            ?>

                            <option value="<?php echo $row['roomID']; ?>">
                                <?php
                                    echo $row['roomname'] . ", "
                                    .$row['roomtype'] . ", "
                                    .$row['beds'];
                                ?>
                            </option>

                            <?php
                                } else{
                                    echo "<options>No room found</options>";
                                }
                            ?>
                        

                        </select>
                    </p>

                    <p>
                        <input type="hidden" name="id" value="<?php echo $id;?>" >
                    </p>

                    <p>
                        <label for="checkinDate">Check-in Date:</label>
                        <input type="text" id="checkinDate" name="checkinDate" required 
                        value="<?php echo $row['checkinDate'];?>" >
                    </p>
                    <p>
                        <label for="checkoutDate">Check-out Date</label>
                        <input type="text" id="checkoutDate" name="checkoutDate" required
                        value="<?php echo $row['checkoutDate'];?>" >
                    </p>
                    <p>
                        <label for="contactNumber">Contact Number:</label>
                        <input type="text" id="contactNumber" name="contactNumber" required 
                        pattern="\(\d{3}\) \d{3} \d{4}"
                        value="<?php echo $row['contactNumber'];?>" >
                    </p>
                    <p>
                        <label for="bookingExtras">Extras:</label>
                        <input type="text" id="bookingExtras" name="bookingExtras"
                        value="<?php echo $row['bookingExtras'];?>" >
                    </p>
                    <p>
                        <label for="bookingReview">Booking Review:</label>
                        <input type="text" id="bookingReview" name="bookingReview"
                        value="<?php echo $row['bookingReview'];?>" >
                    </p>
                    <input type="submit" name="submit" value="Update">
                    <a href="listbookings.php">[Cancel]</a>
            </form>
        </div>
        <br>
        <hr>
        <br>
<?php
    mysqli_free_result($result);
    mysqli_close($DBC);
?>
    <?php 
        if (isset($_SESSION['username'])){
            if (isset($_POST['logout'])) logout();

        $un = $_SESSION['username'];
            if($_SESSION['loggedin'] == 1){ ?>
                <h6>Logged in as <?php echo $un ?></h6>
                <form method="post">
                    <input  type="submit" name="logout" value="Logout"> 
                </form>

	
        <?php 
            }
        }
        ?>
<?php 
    echo '</div></div>';
    include "../footer.php";
?>
</body>
</html>