<?php
require('admin/inc/db_config.php');

// Add payment_status column if it doesn't exist
$query = "SHOW COLUMNS FROM `booking_order` LIKE 'payment_status'";
$result = mysqli_query($con, $query);

if(mysqli_num_rows($result) == 0) {
    // Column doesn't exist, add it
    $alter_query = "ALTER TABLE `booking_order` 
                   ADD COLUMN `payment_status` ENUM('pending', 'partial', 'paid') DEFAULT 'pending' AFTER `booking_status`,
                   ADD COLUMN `amount_paid` DECIMAL(10,2) DEFAULT 0.00 AFTER `payment_status`";
    
    if(mysqli_query($con, $alter_query)) {
        echo "Successfully added payment_status and amount_paid columns to booking_order table.\n";
        
        // Update existing records to mark as paid if booking_status is 'booked'
        $update_query = "UPDATE `booking_order` SET `payment_status` = 'paid', `amount_paid` = `trans_amt` WHERE `booking_status` = 'booked'";
        if(mysqli_query($con, $update_query)) {
            echo "Successfully updated payment status for existing bookings.\n";
        } else {
            echo "Error updating payment status for existing bookings: " . mysqli_error($con) . "\n";
        }
    } else {
        echo "Error adding payment_status column: " . mysqli_error($con) . "\n";
    }
} else {
    echo "payment_status column already exists in booking_order table.\n";
}

echo "\nCurrent booking_order table structure:\n";
$result = mysqli_query($con, "DESCRIBE booking_order");
if($result) {
    echo "Field\t\tType\t\tNull\tKey\tDefault\tExtra\n";
    echo str_repeat("-", 80) . "\n";
    while($row = mysqli_fetch_assoc($result)) {
        echo str_pad($row['Field'], 15) . 
             str_pad($row['Type'], 15) . 
             str_pad($row['Null'], 5) . 
             str_pad($row['Key'] ?: '', 5) . 
             str_pad($row['Default'] ?: 'NULL', 10) . 
             $row['Extra'] . "\n";
    }
} else {
    echo "Error describing table: " . mysqli_error($con) . "\n";
}

// Close connection
mysqli_close($con);
?>
