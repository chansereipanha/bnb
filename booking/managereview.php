<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
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

    function cleanInput($data)
    {
        return htmlspecialchars(stripslashes(trim($data)));
    }

    //check if id exists
    if ($_SERVER["REQUEST_METHOD"] == "GET") {
        $id = $_GET['id'];

        if (empty($id) or !is_numeric($id)) {
            echo "<h2>Invalid ticket id</h2>";
            exit;
        }
    }

    if (isset($_POST['submit']) and !empty($_POST['submit']) and ($_POST['submit'] == 'Update')){
        $bookingReview = cleanInput($_POST['bookingReview']);
        $id = cleanInput($_POST['id']);


        $upd = "UPDATE booking
        SET bookingReview=?
        WHERE bookingID=?";
        $stmt = mysqli_prepare($DBC, $upd);
        mysqli_stmt_bind_param($stmt, 'si', $bookingReview, $id);
        mysqli_stmt_execute($stmt);
        mysqli_stmt_close($stmt);
        echo "<h2>Booking review added</h2>";
    }
    $query = 'SELECT booking.bookingReview
    FROM booking
    WHERE bookingID=' .$id;

    $result = mysqli_query($DBC, $query);
    $rowcount = mysqli_num_rows($result);

?>    
    <h1>Edit/add a room review</h1>
    <h2>
        <a href="listbookings.php">[Return to the Bookings listing]</a>
        <a href="../index.php">[Return to main page]</a>
    </h2>
    <div>
        <form action="managereview.php" method="POST">
            <input type="hidden" name="id" value="<?php echo $id;?>">
            <?php
                if ($rowcount > 0){
                $row = mysqli_fetch_assoc($result);
            ?>
            <p>
                <label for="bookingReview">Booking Review:</label>
                <input type="text" id="bookingReview" name="bookingReview"
                value="<?php echo $row['bookingReview'];?>" >
            </p>

            <?php   
                }else{
                    echo "<h5>No ticket found!</h5>";
                }
            ?>
            <input type="submit" name="submit" value="Update">
        </form>
    </div>

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
</body>



</html>