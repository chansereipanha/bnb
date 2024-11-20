<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Deletion</title>
</head>
<body>
    <?php
        include "config.php";
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

        if (isset($_POST['submit']) and !empty($_POST['submit']) and ($_POST['submit'] == 'Delete')){
            $error = 0;
            $msg = "Error";

            if (isset($_POST['id']) and !empty($_POST['id']) and is_integer(intval($_POST['id']))){
                $id = cleanInput($_POST['id']);
            }
            else{
                $error++;
                $msg .= 'Invalid booking ID';
                $id = 0;
            }
            if ($error == 0 and $id > 0){
                $query = "DELETE FROM booking WHERE bookingID=?";
                $stmt = mysqli_prepare($DBC, $query);
                mysqli_stmt_bind_param($stmt, 'i', $id);
                mysqli_stmt_execute($stmt);
                mysqli_stmt_close($stmt);
                echo "<h2>Booking details deleted</h2>";
            }
            else{
                echo "<h5>$msg</h5>".PHP_EOL;
            }
        }

        $query = 'SELECT booking.bookingID, booking.checkinDate, booking.checkoutDate, room.roomname
        FROM booking
        INNER JOIN room ON booking.roomID = room.roomID
        WHERE bookingID=' .$id;
        $result = mysqli_query($DBC, $query);
        $rowcount = mysqli_num_rows($result);
    ?>



    <h1>Booking preview before deletion</h1>
    <h2>
        <a href="listbookings.php">[Return to the Bookings listing]</a>
        <a href="index.php">[Return to the main page]</a>
    </h2>
    <?php 
        if ($rowcount > 0){
            echo "<fieldset><legend>Booking detail #$id</legend><dl>";
            $row = mysqli_fetch_assoc($result);
            $id = $row['bookingID'];

            echo "<dt>Room Name:</dt> <dd>" . $row["roomname"] . "</dd>" . PHP_EOL;
            echo "<dt>Check-in date:</dt> <dd>" . $row["checkinDate"] . "</dd>" . PHP_EOL;
            echo "<dt>Check-out date:</dt> <dd>" . $row["checkoutDate"] . "</dd>" . PHP_EOL;
            echo '</dl></fieldset>' . PHP_EOL;
        
    ?>
    <form method="POST" action="deletebooking.php">
        <h3>Are you sure you want to delete this Booking?</h3>
        <input type="hidden" name="id" value="<?php echo $id; ?>">
        <input type="submit" name="submit" value="Delete">
        <a href="listbookings.php">[Cancel]</a>            
    </form>
    <?php    
    } else echo "<h2>No Customer found, possbily deleted!</h2>"; //suitable feedback

    mysqli_free_result($result); //free any memory used by the query
    mysqli_close($DBC); //close the connection once done
    ?>
</body>

</html>