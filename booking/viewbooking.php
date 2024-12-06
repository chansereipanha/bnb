<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>View Booking</title>
</head>
<body>
<?php
    include "checksession.php";
        checkUser();

    include "../header.php";

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
            echo "<h2>Invalid Customer ID</h2>"; //simple error feedback
            exit;
        } 
    }


    $query = 'SELECT booking.bookingID, booking.checkinDate, booking.checkoutDate,room.roomname, booking.contactNumber, booking.bookingExtras, booking.bookingReview
    FROM booking
    INNER JOIN room ON booking.roomID = room.roomID
    WHERE bookingID=' .$id;
    $result = mysqli_query($DBC, $query);
    $rowcount = mysqli_num_rows($result);
    ?>



    <h1>Booking preview before deletion</h1>
    <h2>
        <a href="listbookings.php">[Return to the Bookings listing]</a>
        <a href="../index.php">[Return to the main page]</a>
    </h2>
    <?php 
        if ($rowcount > 0){
            echo "<fieldset><legend>Booking detail #$id</legend><dl>";
            $row = mysqli_fetch_assoc($result);
            $id = $row['bookingID'];

            echo "<dt>Room Name:</dt> <dd>" . $row["roomname"] . "</dd>" . PHP_EOL;
            echo "<dt>Check-in date:</dt> <dd>" . $row["checkinDate"] . "</dd>" . PHP_EOL;
            echo "<dt>Check-out date:</dt> <dd>" . $row["checkoutDate"] . "</dd>" . PHP_EOL;
            echo "<dt>Contact Number:</dt> <dd>" . $row["contactNumber"] . "</dd>" . PHP_EOL;
            echo "<dt>Extras:</dt> <dd>" . $row["bookingExtras"] . "</dd>" . PHP_EOL;
            echo "<dt>Room review:</dt> <dd>" . $row["bookingReview"] . "</dd>" . PHP_EOL;
            echo '</dl></fieldset>' . PHP_EOL;
        }
    ?>
    <br>
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