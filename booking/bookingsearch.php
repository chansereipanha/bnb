<?php
    include "../config.php";

    $fromDate = $_GET['fromDate'];
    $toDate = $_GET['toDate'];

    $DBC = new mysqli(DBHOST, DBUSER, DBPASSWORD, DBDATABASE);

    if ($DBC->connect_errno) {
        echo "Error: Unable to connect to MySQL. " . $DBC->connect_error;
        exit;
    }

    $query = "SELECT * 
        FROM room 
        WHERE roomID 
        NOT IN (SELECT roomID FROM booking 
        WHERE checkinDate >= '$fromDate' AND checkoutDate <= '$toDate')";

        $stmt = mysqli_prepare($DBC, $query);
        mysqli_stmt_execute($stmt);
        $result = mysqli_stmt_get_result($stmt);
        
        if ($result) {
            if (mysqli_num_rows($result) > 0) {
                while ($row = mysqli_fetch_assoc($result)) {
                    echo "<tr>";
                    echo "<td>" . $row['roomID'] . "</td>";
                    echo "<td>" . $row['roomname'] . "</td>";
                    echo "<td>" . $row['roomtype'] . "</td>";
                    echo "<td>" . $row['beds'] . "</td>";
                    echo "</tr>";
                }
            } else {
                echo "No available rooms found for the selected date range.";
            }
        } else {
            // Handle query error
            echo "Error executing the query: " . $DBC->error;
        }
        
        // Close the statement
        mysqli_stmt_close($stmt);
        
        // Close database connection
        mysqli_close($DBC);
?>