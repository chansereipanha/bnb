<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Current Bookings</title>
</head>
<body>
    <?php
        include 'checksession.php';
            checkUser();
    
        include "../config.php";
        $DBC = mysqli_connect(DBHOST, DBUSER, DBPASSWORD, DBDATABASE);

        if (mysqli_connect_errno()) {
            echo "Error:Unable to connect to MySQL." . mysqli_connect_error();
            exit; 
        }


        $query = 'SELECT booking.bookingID, room.roomname, booking.checkinDate, booking.checkoutDate, customer.lastname, customer.firstname
        FROM room, booking, customer
        WHERE booking.roomID = room.roomID and booking.customerID = customer.customerID
        ORDER BY bookingID';
        $result = mysqli_query($DBC, $query);
        $rowcount = mysqli_num_rows($result);
    ?>
    <h1>Current booking</h1>
    <h2>
        <a href="makeabooking.php">[Make a booking]</a>
        <a href="../index.php">[Return to main page]</a>
    </h2>

    <table border="1">
        <thead>
            <tr>
                <th>Booking(room,dates)</th>
                <th>Customer</th>
                <th>Action</th>
            </tr>
        </thead>

        <?php
            if ($rowcount > 0) {
                while ($row = mysqli_fetch_assoc($result)) {
                    $id = $row['bookingID'];
                    echo '<tr><td>' .$row['roomname'] . ', ' . $row['checkinDate'] .', ' .$row['checkoutDate'] . '</td>';
                    echo '<td>'. $row['lastname'] .', '. $row['firstname']. '</td>';
                    echo '<td> <a href="viewbooking.php?id='.$id.'"> [view]';
                    echo '  <a href="editbooking.php?id='.$id.'"> [edit]';
                    echo '  <a href="managereview.php?id='.$id.'"> [manage reviews]';
                    echo '  <a href="deletebooking.php?id='.$id.'"> [delete] </td>';
                    echo '</tr>' . PHP_EOL;
            }} else 
                echo "<h2>No tickets found</h2>";

            mysqli_free_result($result);
            mysqli_close($DBC);

        ?>

    </table>

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


</body>
</html>