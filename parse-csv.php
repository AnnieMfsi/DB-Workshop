<?php
/**
 * Script to parse the given csv file.
 * Store the data from csv to database.
 * Validate the data and display in the browser.
 */

ini_set( 'display_startup_errors', 1 );
ini_set( 'display_errors', 1 );

$start = microtime( true );

// Check if the csv file exist.
try {
	if ( ! file_exists( 'Test - Parse Sheet.csv' ) ) {

		throw new Exception( 'File does not exist!' );
	}
	$file = fopen( 'Test - Parse Sheet.csv', 'r' );
	$i = 0;

	// Continue till it reaches the last line.
	while ( ( $line = fgetcsv( $file ) ) !== false ) {

		// $line is an array of the csv elements.
		$array[ $i ] = explode( ';', $line[0] );

		$i++;
	}

	fclose( $file );

	$csv_header_format = [ 'EmpID', 'Name', 'Last', 'Skill1', 'Skill2', 'Skill3', 'Skill4', 'Skill5', 'StackID', 'StackNickname', 'CreatedBy', 'UpdatedBy' ];

	// Column headings, 1st row of csv.
	$header = $array[0];

	$header = array_map( 'stripslashes', $header );
	$header = array_map( 'strip_tags', $header );

	// Validate if the csv file is in required format.
	if ( $header !== $csv_header_format ) {
		die( 'Sorry! your csv file format do not match' );
	}

	// Array for csv data as array[column_name]=>value
	$csv_data = array();

	// For each row of values.
	foreach ( $array as $key => $value ) {

		// 0th row is header.
		if ( 0 !== $key ) {

			$csv_data[] = array_combine( $header, $value );
		}
	}

	$stack_query = array();
	$skill_list = array();
	$hr_list = array();
	$row_skills = array();

	// Hardcoding the column names for inserting data in tables.
	$emp_columns = 'emp_id, emp_fname, emp_lname, emp_fk_hr_created_by, emp_fk_hr_updated_by'; // For employee table.
	$stack_columns = 'emp_auto_id_fk, stack_id, stack_name'; // For stack table.
	$skill_known_columns = 'fk_emp_id, fk_skill_list_id'; // For skill known column.

	// Accessing through each row of csv data in form of array.
	foreach ( $csv_data as $row ) {

		array_push( $hr_list, $row['CreatedBy'], $row['UpdatedBy'] );
		array_push( $skill_list, $row['Skill1'], $row['Skill2'], $row['Skill3'], $row['Skill4'], $row['Skill5'] );
	}

	// Calling function to createdb connection.
	$conn = create_mysqli_connection();

	// Insert data into hr and skill table and fetch the array in form list[auto_id]=>name.
	$hr_list = ws_hr_skill_table( $conn, 'ws_hr_list', 'hr_name', $hr_list );
	$skill_list = ws_hr_skill_table( $conn, 'ws_skill_list', 'skill_name', $skill_list );

	// Access each cvs array(row) to insert data into tables.
	foreach ( $csv_data as $row ) {

		// Replace the names of the HR by their id.
		$row['CreatedBy'] = array_search( $row['CreatedBy'] , $hr_list );
		$row['UpdatedBy'] = array_search( $row['UpdatedBy'] , $hr_list );

		// Values to be inserted into the employee_detail table.
		$emp_values = '("' . $row['EmpID'] . '","' . $row['Name'] . '", "' . $row['Last'] . '", "' . $row['CreatedBy'] . '", "' . $row['UpdatedBy'] . '")';
		$emp_id = ws_insert_query( $conn, 'ws_employee_details', $emp_columns, $emp_values );

		// Values to be inserted into the stack_detail table.
		$stack_values = '("' . $emp_id . '","' . $row['StackID'] . '", "' . $row['StackNickname'] . '")';
		$id = ws_insert_query( $conn, 'ws_stack_detail', $stack_columns, $stack_values );

		// Create array for skills in arow.
		array_push( $row_skills, $row['Skill1'], $row['Skill2'], $row['Skill3'], $row['Skill4'], $row['Skill5'] );

		// Remove the empty skills.
		$row_skills = array_filter( $row_skills );

		foreach ( $row_skills as $key => $skill ) {

			// Replace the skill with its id.
			$row_skills[ $key ] = array_search( $skill , $skill_list );

			// Values to be inserted into the stack_detail table.
			if ( 0 !== $emp_id ) {
				$skill_row_values = '( "' . $emp_id . '", "' . $row_skills[ $key ] . '" )';
				$id = ws_insert_query( $conn, 'ws_emp_skill_list', $skill_known_columns, $skill_row_values );
			}
		}

		$emp_values = array();
		$stack_values = array();
		$row_skills = array();
	}

	mysqli_close( $conn );

	$end = microtime( true ); ;

	echo 'Data Successfully inserted in ' . ( $end - $start ) ;

} catch ( Exception $e ) {
	echo 'Message: ' . $e->getMessage();
} // End try().


/**
 * To create database connection.
 *
 * @param
 *
 * @return {object} $conn - db connection object
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

/**
 * To insert data into the employee_detail, emp_skill ans stack detail table.
 * If connection is not successful, die
 *
 * @param {object} $conn   - db connection object
 * @param {string} $table  - name of the table to insert data
 * @param {string} $column - comma separated name of columns to insert data
 * @param {string} $values - comma separated values to be inserted.
 *
 * @return {int} $id - value of last inserted id
 */
function ws_insert_query( $conn, $table, $column, $values ) {

	ws_query_operation( $conn, $table, $column, $values );

	$id = mysqli_insert_id( $conn );
	return $id;
}

/**
 * To insert data into the hr and skill table
 * If connection is not successful, die
 *
 * @param {object} $conn   - db connection object
 * @param {string} $table  - name of the table to insert data
 * @param {string} $column - comma separated name of columns to insert data
 * @param {array}  $array - array of hr/skill names.
 *
 * @return {array} $array_new - updated array with auto ids of hr/skill
 */
function ws_hr_skill_table( $conn, $table, $column, $array ) {

	$array = array_filter( array_unique( $array ) );

	// Converting the array list to multiple value format.
	$col = "('" . implode( "'),('", $array ) . "')";

	ws_query_operation( $conn, $table, $column, $col );

	$id = mysqli_insert_id( $conn );
	$array_new = array();
	// Assign the id with the value of each data.
	foreach ( $array as $key => $value ) {
		$array_new[ $id ] = $array[ $key ];
		unset( $array[ $key ] );
		$id++;
	}
	return $array_new;
}

/**
 * To execute query operation for insertion
 * If connection is not successful, die
 *
 * @param {object} $conn   - db connection object
 * @param {string} $table  - name of the table to insert data
 * @param {string} $column - comma separated name of columns to insert data
 * @param {string} $values - comma separated values to be inserted.
 *
 * @return void
 */
function ws_query_operation( $conn, $table, $column, $data ) {

	$sql = "INSERT IGNORE INTO $table( $column ) VALUES" . $data;
	$result = mysqli_query( $conn, $sql );

	if ( ! $result ) {
	    die( 'Error : ' . mysqli_connect( $result ) );
	}
}
