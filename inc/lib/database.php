<?php
//This file is responsible for handling database queries and commands

require_once "/inc/config.php";
require_once LOGIN_PATH; 
require_once UNITS_TABLE_PATH;

//Define the error codes
define( 'SUCCESS'	, 	0 );
define( 'PREP_FAIL'	, 	1 );
define( 'EXEC_FAIL'	, 	2 );

class Database_handler
{
    //%%%%%%%%%%%   FIELDS   %%%%%%%%%%%%%%
    protected $conn; //the connection variable for the database session
    protected $command; //the actual sql query to be sent to the database

    
    //%%%%%%%%%%%   FUNCTIONS   %%%%%%%%%%%%%%
    /**
     *	get_command()
     *	=============
     *
     * returns the value of $command, the sql command to be sent to the database
     */
    function get_command()
    {
        return $command;
    }


    /**
     *	open_connection()
     *	================
     *
     *	this function opens the connection using the database host, 
     *	name, and sql username and password specified in LOGIN_PATH
     */
    protected function open_connection(){
	    $this->conn = new PDO( 'mysql:host=' . DB_HOST . ';dbname=' . 
                DB_NAME, SQL_USERNM, SQL_PSWD );
    }


    /**
     *	insert_row()
     *	================
     *
     * inserts a row into the specified table with data 
     * according to the $params input
     *
     * @param 	- $table	-a string specifying the table name
     *
     * @param 	- $params 	-an associative array with keys equal to 
     * 				    the column name and values equal to the 
     * 				    row value.
     * 				    Ex: array( 'serving_size' => 10 )
     *
     * @return 	- SUCCESS 	-If the command executed successfully
     *
     * 	        - PREP_FAIL	-If the command preparation failed
     *
     * 		- EXEC_FAIL 	-If the command execution failed
     */
    function insert_row( $table, $params )
    {
        $this->open_connection();

        //TODO: TEST THIS!
        $command = 'INSERT INTO ' . $table . ' (';
        
        //build the query based off the contents of $params
        foreach( $params as $col => $row) 
        {
            $command .= $col . ', ';
        }
        
        $command = substr( $command, 0, -2 ); //remove last comma
        $command .= ') VALUES (';

        foreach( $params as $col => $row)
        {
            $command .= ':' . $col . ', ';
        }

        $command = substr( $command, 0, -2 ); //remove last comma
        $command .= ')';

        //$formatted_params has same format as $params, but the keys (or column 
        //names) start with a colon (:)
        $formatted_params = array();    
        foreach( $params as $col => $row )
        {
            $formatted_params[ ':' . $col ] = $row;
        }

        //prepare the sql statement
        $query = $this->conn->prepare( $command ); 
        if( !$query )
        {
	    echo 'Query preparation failed! - (' . $query->errno . ') ' . 
                $query->error;

            $this->close_connection();
            return -1;
        }
        
        //crank the parameters into the statement and execute
        $query = $query->execute( $params ); 
        if( !$query )
        {
	    echo 'Query execution failed! - (' . $query->errno . ') ' . 
                $query->error;

            $this->close_connection();
            return -1;
        }

        $this->close_connection();
    }


    /**
     * query_table()
     * =============
     *
     * @param   - $command          -The actual SQL command to be sent
     *                                  e.g. - SELECT * from t_foods
     *
     * @returns - $results
     */
    function query_table( $command )
    {
        //TODO: test this
        $this->open_connection();
        $response = $this->conn->query( $command );

        if( !$response )
        {
            echo 'Query error while searching a table. Printing stack ' .
                'backtrace...';
            var_dump( debug_backtrace() );
            $this->close_connection();
            return null;
        }

        $results = $response->fetchAll();
        $this->close_connection();

        return $results;
    }


    /**
     * get_foods()
     * ===========
     * Retrieve all foods from the db. Return them all in array format
     *
     * @param   - $db       - a Database_handler object
     *
     * @returns - $foods    - the array of saved foods.  Each element has 
     *                          the following structure:
     *                          array(
     *                              'id',
     *                              'esha_food_id',
     *                              'name'
     *                              'serving_size'
     *                              'serving_units'
     *                              'cost'
     *                              'calories'
     *                              'esha_info'
     *                          )
     */
    function get_foods()
    {
        global $code_to_unit_table;
        $foods = array();

        $command = "SELECT * FROM t_foods";
        $results = $this->query_table( $command );

        foreach( $results as $result )
        {
            $foods[$result['id']] = array(
                'id'            => $result['id'],
                'esha_food_id'  => $result['esha_food_id'],
                'name'          => $result['user_def_food_name'],
                'serving_size'  => $result['serving_size'],
                'serving_units' => 
                    $code_to_unit_table[$result['serving_units_esha']],
                'cost'          => $result['cost'],
                'calories'      => $result['calories'],
                'esha_info'     => json_decode(
                                    stripslashes($result['json_esha']))
            );
        }

        return $foods;
    }

    /**
     *	close_connection()
     *	================
     *
     *	this function closes the database connection 
     */
    protected function close_connection(){
	    $conn = null; //close the connection by setting it to null
    }
}