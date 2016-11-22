<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="utf-8">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="description" content="">
        <meta name="author" content="">
        <title>Employee Detail</title>
    </head>
	<style>

	table {
	    font-family: arial, sans-serif;
	    border-collapse: collapse;
	    width: 100%;
	}
	th {
	    background-color: #D50000;
	    color: white;
	}
	td, th {
	    border: 1px solid #dddddd;
	    text-align: left;
	    padding: 8px;
	}
	tr:nth-child(even) {
	    background-color: #dddddd;
	}
	</style>
    <body>
<?php

// Calling function to createdb connection.
$conn = create_mysqli_connection();

$sql = "SELECT emp.emp_id, emp.emp_fname, emp.emp_lname, GROUP_CONCAT(skill.skill_name ORDER BY eskill.emp_skill_auto_id ASC) skills, estack.stack_id, estack.stack_name, hr1.hr_name Created_by, hr2.hr_name Updated_by
	FROM ws_employee_details emp
	LEFT JOIN ws_emp_skill_list eskill ON emp.emp_auto_id = eskill.fk_emp_id
	LEFT JOIN ws_skill_list skill ON eskill.fk_skill_list_id = skill.skill_auto_id
	INNER JOIN ws_stack_detail estack ON estack.emp_auto_id_fk = emp.emp_auto_id
	INNER JOIN ws_hr_list hr1 ON emp.emp_fk_hr_created_by = hr1.hr_auto_id
	INNER JOIN ws_hr_list hr2 ON emp.emp_fk_hr_updated_by = hr2.hr_auto_id

	GROUP BY emp.emp_id
	ORDER BY emp.emp_auto_id ASC";

$result = mysqli_query( $conn, $sql );

if ( ! $result ) {
	die( 'Could not get data: ' . mysql_error() );
}

// Creating html for table.
echo '<table align="center" ><thead><tr><th>EmpID</th>
            <th>First Name</th><th>Last Name</th><th>SKill1</th><th>SKill2</th><th>SKill3</th><th>SKill4</th><th>SKill5</th><th>Stack ID</th><th>Stack Name</th>
            <th>Created By</th><th>Updated By</th></tr></thead><tbody>';

// For all the records
while ( $row = mysqli_fetch_array( $result, MYSQLI_ASSOC ) ) {
	echo '<tr>';
	foreach ( $row as $key => $value ) {

		// For the comma separated skills
		if ( 'skills' === $key ) {
			$skills = explode( ',', $value );
			for (  $i = 0; $i < 5; $i++ ) {
				echo '<td>';
				echo ( isset( $skills[ $i ] ) ?  $skills[ $i ] : '' );
				echo '</td>';
			}
		} else {
			echo '<td>' . $value . '</td>';
		}
	}
	echo '</tr>';
}
echo '</tbody></table>';

mysqli_close( $conn );

/**
 * To create db connection
 *
 * @param
 * @return {object} $conn - connection object
 */
function create_mysqli_connection() {

	$servername = 'localhost';
	$username = 'root';
	$password = 'mfsi1234';
	$db_name = 'Workshop_db';

	// Create connection.
	$conn = mysqli_connect( $servername, $username, $password, $db_name );

	// Check connection.
	if ( $conn->connect_error ) {
		die( 'Connection failed: ' . $conn->connect_error );
	}
	return $conn;
}
?>
	</body>
</html>
