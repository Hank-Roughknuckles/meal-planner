<?php
//TODO: make individual folders for URLs. (i.e. - instead of having /new_food.php, have /newFood/, or something)
//TODO: make the program inform the user if there is already a food saved of the same type as what they are entering
//TODO: parse the esha query results into a class that you can standardize and work with
//TODO: implement error handling in case the user inputs a letter where only numbers should be.
require_once "/inc/config.php";
require_once LOGIN_PATH;
require_once INCLUDE_PATH . "esha.php";

session_start();

$_SESSION['page_title'] = "New Food";
$_SESSION['user_id'] = "-1"; //TODO: implement user accounts since THIS IS JUST FOR TESTING UNTIL WE IMPLEMENT USER ACCOUNTS

//%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%
//%																		   %
//%								functions 								   %	
//%																		   %
//%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%

/**
*	display_page_header()
*	=====================
*
*	Takes in a string and sets the title of the page and the headline
*	to it
*
*	@param 	-	$inTitle 	-	the title to be displayed
*
*
*	@return -	NULL
*/
function display_page_header( $inTitle )
{
	$pageTitle = $inTitle;
	include HEADER_PATH;
}


/**
*	create_serving_units_dropdown()
*	===============================
*
*	creates a "select" input (aka dropdown menu) for a form based on the 
*	arguments passed
*
*	@param 	$units 	-	A one dimensional array containing the units that 
*						will be in the dropdown menu
*
*						Ex: $units = array("Cup", "Milliliter", "Pound")
*
*
*	@return NULL
*
*/
function create_serving_units_dropdown( $units )
{
	//TODO: modify this so that if the serving size is > 1, the units will have an 's' at the end. This will probably require some javascript

	require_once( UNITS_TABLE_PATH );

	//the serving units (i.e. cups, pieces, lbs, etc.) input
	echo '<select name="serving_units">';

	//output each unit to the dropdown list
	foreach( $units as $unit )
	{
		echo '<option value="' . $unit . '">' . $unit . '(s)</option>';
	}

	echo '</select>';
}


/**
*	create_pantry_save_form()
*	=========================
*
*	Creates a form in HTML that has the layout for saving a food in the user's
*	pantry. This form has information for the user to input like food name,
*	serving size, serving units, Cost per serving size, and the currency that
*	cost is denominated in.
*
*	@param 	-	$default_food_name	-	The name of the food that will show up
*										in the "food name" field when the page
*										first loads up.
*
*	@param 	-	$unit_list			-	an array containing the desired serving
*										units to be displayed
*
*	@return -	NULL
*
*/
function create_pantry_save_form( $default_food_name, $unit_list )
{
	//TODO: make this return a string that contains all the html code instead of directly outputting it through echo.
	echo '<form name="input" action="' . BASE_URL . 'new_food.php' . '" method="post">';
	echo 	'<table>';
	echo 		'<tr>';
	echo 			'<th><label for="user_def_food_name">Name:</label></th>';
	echo 			'<td><input type="text" name="user_def_food_name" id="user_def_food_name" value="' . $default_food_name . '" size="' . (strlen($default_food_name) + 5) . '"></td>';
	echo 		'</tr>';
	echo 	'</table>';
	echo 	'<p>How much is it?</p>';
	echo 	'<table>';
	echo 		'<tr>';
	echo 			'<th>Amount</th>';
	echo 			'<th>Units</th>';
	echo 		'</tr>';
	echo 		'<tr>';
	echo 			'<td>';
	echo 				'<input type="text" name="serving_size" id="serving_size" value="1">';
	echo 			'</td>'; //TODO: do form validation for this text input to make sure all the inputs are in number fomat
	echo 			'<td>';
	create_serving_units_dropdown( $unit_list );
	echo 			'</td>';
	echo 		'</tr>';
	echo 	'</table>';

	echo 	'<p>Costs</p>';

	echo 	'<table>';
	echo 		'<tr>';
	echo 			'<td>';
	echo 				'<input type="text" name="cost" value="">';
	echo 			'</td>';
	echo 			'<td>';
	create_currency_dropdown();
	echo 			'</td>';
	echo 			'<td>';
	echo 				'<input type="hidden" name="status" value="save_food">'; //tells the site to save the food in the database if this is selected
	echo 				'<input type="submit" value="Save that food!">';
	echo 			'</td>';
	echo 		'</tr>';
	echo 	'</table>';
	echo '</form>';
}



/**
*	create_currency_dropdown()
*	==========================
*
*	creates a dropdown menu for currencies based on the passed arguments.
*	If the $default_currency argument is not an element in the 
*	$currencies array, then there will be no default element in the 
*	dropdown menu and the function will return false.
*
*	@param 	$currencies	-	A one dimensional array containing the currencies
*							that will be in the dropdown menu
*
*							Ex: $units = array("USD", "EUR", "JPY");
*
*	@param 	$default_currency	-	a string containing the currency that 
*									will be selected as the default for the
*									dropdown menu
*
*
*	@return $isPresent			-	true if $default_currency is present
*										in the $currencies array
*									false if $default_currency is not 
*										present in the array
*/
function create_currency_dropdown( $currencies = NULL, $default_currency = "USD" )
{
	//initialize the currencies array if nothing is passed as an argument
	if( $currencies == NULL )
	{
		$currencies = array(
			"USD",
			"KRW",
			"EUR",
			"GBP",
			"RON"
			);
	}

	//build the dropdown menu
	$isPresent = false;
	echo '<select name="currency">';
	foreach( $currencies as $currency )
	{
		if( $currency == $default_currency )
		{
			echo '<option value="' . $currency . '" selected>' . $currency . '</option>';
			$isPresent = true;
		}
		else
		{
			echo '<option value="' . $currency . '">' . $currency . '</option>';
		}
	}
	echo '</select>';


	return $isPresent;
}



/**
*	my_var_dump()
*	=============
*
*	displays the name of the variable to be displayed in var_dump() 
*	and then calls var_dump() after
*
*	@param 	-	 $var_name 	-	 the name to be displayed before var_dump
*
*	@param 	-	 $variable 	-	the variable to be fed into var_dump
*
*
*	@return -	NULL
*/
function my_var_dump( $var_name, $variable )
{
	echo " VAR_DUMP OF $var_name";
	echo "<pre>";
	var_dump( $variable );
	echo "</pre>";
}

//
//%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%
//%																			%
//%								POST handling								%
//%																			%
//%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%
if( $_SERVER["REQUEST_METHOD"] == "POST")
{
	$error_array = array();

	// ==================================================================
	//
	// 1st step - After the user has searched for a food name
	//
	// ------------------------------------------------------------------
	if( $_POST["status"] == "name_selected" )
	{
		//Check if the food name is blank
		if( !isset($_POST["name"]) OR trim($_POST["name"]) == "" )
		{
			//if no food name was searched, mark it in the error array and go back to the initial page
			$error_array["name"] = true;
			// header( "Location: " . BASE_URL . "new_food.php" );
		}
		else
		{
			//store entered name as session variable
			$_SESSION['food_name_query'] = htmlspecialchars( $_POST['name'] );

			// if no errors, proceed to the next step, setting $_GET['status']='find'
			header( "Location: " . BASE_URL . "new_food.php?status=find" );
		}
	}


	// ==================================================================
	//
	//	Searching for nutrition facts
	//
	// ------------------------------------------------------------------
	else if( $_POST['status'] == 'nutrition_facts' )
	{
		require_once( UNITS_TABLE_PATH );

		//these two variables are to be used in searching for nutrition facts, they are not necessarily the units that will be used to store the food in the user's pantry (db).  I decided to use _SESSION instead of using GET to keep things more secure
		$_SESSION['lookup_serving_size'] = $_POST['serving_size'];

		//since $units_lookup_table is structured as $id => $unit_name, search through the table to find which id matches the POSTed unit
		foreach( $units_lookup_table as $id => $table_unit )
		{
			if( $table_unit == $_POST['serving_units'] )
			{
				$_SESSION['lookup_serving_units_id'] = $id;
			}
		}

		header( "Location: " . BASE_URL . "new_food.php?status=nutrition_facts" );
	}


	// ==================================================================
	//
	//	The last step - saving the user's choices to the database
	//	(Note: the steps in between are below - in the non-POST section)
	//
	// ------------------------------------------------------------------
	else if( $_POST["status"] == "save_food" )
	{
		//import the variables from _POST and _SESSION
		$user_id				= trim( $_SESSION['user_id'] ); //the id of the user currently logged in
		$user_def_food_name 	= trim( $_POST['user_def_food_name'] ); //the name the user saved the food as
		$serving_size 			= $_POST['serving_size'];
		$serving_units_esha		= trim( $_POST['serving_units'] );
		$cost 					= $_POST['cost']; //this is the cost per serving size specified in $serving_size
		$currency 				= trim( $_POST['currency'] );
		$json_esha				= json_encode( $_SESSION['selected_food'] ); //the json encoded esha information about the food
		$esha_food_id			= trim( $_SESSION['selected_food']->id ); //the food id as found in the esha database


		// replace any blank fields with NULL instead
		if( $user_def_food_name == "" ){
			$user_def_food_name = NULL;
		}
		if( $serving_size == "" ){
			$serving_size = NULL;
		}
		if( $serving_units_esha == "" ){
			$serving_units_esha = NULL;
		}
		if( $cost == "" ){
			$cost = NULL;
		}
		if( $currency == "" ){
			$currency = NULL;
		}

		//TODO: do any error checking / form validation here
		$error_array = array();

		//escape all double quotation marks in json_esha
		$json_esha = addslashes( $json_esha );


		// Set up and insert data into the database if there are no errors
		if( count( $error_array ) == 0 ){

			//using PDO prepared statements...
			$conn = new PDO( 'mysql:host=' . DB_HOST . ';dbname=' . DB_NAME, SQL_USERNM, SQL_PSWD );

			$sql = 'INSERT INTO t_foods (user_def_food_name, serving_size, serving_units_esha, cost, currency, json_esha, esha_food_id, user_id) VALUES (?, ?, ?, ?, ?, ?, ?, ?)';

			//the params to go into the ?'s in the $sql variable
			$params = array(
				$user_def_food_name,
				$serving_size,
				$serving_units_esha,
				$cost,
				$currency,
				$json_esha,
				$esha_food_id,
				$user_id
			);

			//prepare the sql statement
			$query = $conn->prepare( $sql ); 
			if( !$query )
			{
				echo 'Query preparation failed! - (' . $query->errno . ') ' . $query->error;
			}

			//crank the parameters into the statement and execute
			$query = $query->execute( $params ); 
			if( !$query )
			{
				echo 'Query execution failed! - (' . $query->errno . ') ' . $query->error;
			}

			$conn = null; //close the connection by setting it to null
		}
		echo '<p>Food Saved!</p>';
	}
} //end if request method == POST


//%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%
//%																		   %
//%								non-POST stuff							   %	
//%																		   %
//%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%

// ==================================================================
//
// Step 1 - Searching by food name
//
// ------------------------------------------------------------------
if( !isset( $_GET['status'] ) )
{
	//display the page title
	display_page_header( $_SESSION['page_title'] );

	echo '<p>Search for a food to add to your pantry</p>';

	if( isset($error_array["name"]) AND $error_array["name"] == true )
	{
		echo '<p>Please enter a food name</p>';
	}

	echo '<form name="input" action="' . BASE_URL . 'new_food.php' . '" method="post">';
	echo '<input type="text" name="name" value="">';
	echo '<input type="hidden" name="status" value="name_selected">'; //since there are multiple posts on this page, this field tells the site that the first stage, the food name submission stage is complete
	echo '<input type="submit" value="Find Food">';
}


// ==================================================================
//
// Step 2 - Choosing a food from the returned database possibilities
//
// ------------------------------------------------------------------
else if( isset($_GET['status']) AND $_GET['status'] == "find" ) 
{
	//display the page title
	display_page_header( htmlspecialchars( $_SESSION['page_title']. ' - Search Results for "' . $_SESSION['food_name_query'] ) . '"');

	//get the list of foods that match the user-defined query
	$search_result = json_decode( file_get_contents( "http://api.esha.com/foods?apikey=" . ESHA_API_KEY . "&query=" . urlencode( $_SESSION['food_name_query'] ) . '&spell=true' ) ); 
	$search_result = $search_result->items;
	$_SESSION['matched_foods'] = $search_result;


	//put a table at the bottom of the screen displaying all the results
	echo '<table>';
	$i = 0;

	foreach( $search_result as $food ) {
		echo '<tr>';
		echo '<td>' . htmlspecialchars( $food->description ) . '</td>';

		//make links that correspond to each returned food match. idx in the GET corresponds to the index of the selected food in the $_SESSION['matched_foods'] array
		echo '<td><a href="' . BASE_URL . 'new_food.php?status=food_selected&idx=' . $i . '">Select</a></td>';
		$i++;

		echo '</tr>';
	}
	echo '</table>';
}


// ==================================================================
//
// Step 3:	Specifying cost per serving for the food
//
// ------------------------------------------------------------------
else if( isset($_GET["status"]) AND $_GET["status"] == "food_selected" )
{
	//TODO: use AJAX (eventually) to show nutrition facts as the user changes the serving size on this page

	//retrieve the selected food from the matched_foods array dependent on what idx is in the GET variable
	$_SESSION['selected_food'] = $_SESSION['matched_foods'][ $_GET['idx'] ];
	$selected_food = $_SESSION['selected_food'];

	//for readability
	$food_name = $selected_food->description;

	//display the page title
	display_page_header( $_SESSION['page_title'] . ' - ' . $food_name );

	$units = create_units_array( $_SESSION['selected_food'] );
	?>

	<!-- //TODO: Hopefully this will look prettier with some CSS -->
	<!-- //give the user the option to search for the food's nutrition facts... -->
	<hr>
	<h3>Search for Nutrition Facts</h3>
	<form name="input" action="<?php echo BASE_URL . 'new_food.php'; ?>" method="post">
		<table>
			<tr>
				<th>Amount</th>
				<th>Units</th>
			</tr>

			<tr>
				<td><input type = "text" name="serving_size" value=""></td> <!-- //TODO: do form validation for this text input to make sure that it all numbers input the serving size input -->
				<td>
					<?php create_serving_units_dropdown( $units ); ?>
				</td>
				<td>
					<input type="hidden" name="status" value="nutrition_facts"><!--//tells the site to view the nutrition facts if this is selected -->
					<input type="submit" value="See Nutrition Facts">
				</td>
			</tr>
		</table>
	</form>


	<h2>OR</h2>


	<!-- ...or give them the option to save the food in the database -->
	<h3>Save the food in your pantry</h3>

	<?php
	create_pantry_save_form( $selected_food->description, $units );	
}


// ==================================================================
//
// Step 3.5 - Look at food nutrition facts
//
// ------------------------------------------------------------------
else if( isset($_GET['status']) AND $_GET['status'] == 'nutrition_facts' )
{
	require( UNITS_TABLE_PATH );
	require_once( NUTRIENTS_TABLE_PATH );

	display_page_header( "Nutrition Facts - " . $_SESSION['selected_food']->description );
	$nutrition_facts = fetch_food_details( 
		$_SESSION['selected_food']->id, 
		$_SESSION['lookup_serving_size'], 
		$_SESSION['lookup_serving_units_id'],  
		ESHA_API_KEY
	);

	//show the serving size and units
	echo '<table>';
	echo 	'<tr>';
	echo 		'<th>Serving Size:</th>';

	//put the units in plural if the serving is != 1
	//TODO: implement inch'es' instead of 'inchs'.  Make the units correctly either have 's' or 'es' at the end, depending on the word
	if(	$_SESSION['lookup_serving_size'] == 1 )
	{
		echo 	'<td>' . $_SESSION['lookup_serving_size'] . ' ' . $units_lookup_table[ $_SESSION['lookup_serving_units_id'] ];
	}
	else
	{
		echo 	'<td>' . $_SESSION['lookup_serving_size'] . ' ' . $units_lookup_table[ $_SESSION['lookup_serving_units_id'] ] . 's';
	}
	echo 	'</tr>';

	//show each of the nutrients in it
	foreach ($nutrition_facts as $fact) 
	{
		echo '<tr>';

		$nutrient = $nutrients_lookup_table[ $fact->nutrient ]['description'];
		$nutrient_unit = $nutrients_lookup_table[ $fact->nutrient ]['unit']; 

		echo '<th>' . $nutrient . ':</th>';
		echo '<td>' . $fact->value . ' ' . $nutrient_unit . '</td>';

		echo '</tr>';
	}
	echo '</table>';

	echo '<hr>';
	echo '<p>' . htmlspecialchars('If you want to save this food in your pantry, please specify how much it costs:') . '</p>';

	$units = create_units_array( $_SESSION['selected_food'] );
	create_pantry_save_form( $_SESSION['selected_food']->description, $units );
}


// ==================================================================
//
// Step 4 - Food saved successfully
//
// ------------------------------------------------------------------
else if( isset($_GET["status"]) AND $_GET["status"] == "submitted" )
{
					//display the page title
	display_page_header( "Save Successful" );

	echo "<p>Food saved!</p>";
	echo '<a href="' . BASE_URL . 'new_food.php">Enter a new food</a>';
	exit();
}
?>

<?php 
include( FOOTER_PATH );
