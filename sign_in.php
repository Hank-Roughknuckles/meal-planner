<?php
require_once("/inc/config.php");
require_once LIB_PATH.'PasswordHash.php';
require_once LIB_PATH.'validator.php';
require_once DB_PATH;

//Display the header
$pageTitle = "Sign in";
include( HEADER_PATH );

session_start();

//%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%
//%
//% 		      	                FUNCTIONS
//%
//%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%
/**
 * make_sign_in_form()
 * ===================
 *
 * Returns the html for the form where the user will enter his or her 
 * login information in order to sign in
 */
function make_sign_in_form()
{
  //TODO: this sign_in_form() function should take in an array of errors 
  //depending on what portion of the form was not filled out. The form 
  //should make these invalid fields have a class of form-error
  $form_html = '<form name = "input" action="'.SIGN_IN_PATH.'" 
    method="post">';
  $form_html .= '<table>';

  //Email address
  $form_html .= '<tr>';
  $form_html .= '<td><th><label for="email">Email Address:</th></td>';
  $form_html .= '<td><input type="text" name="email" id="email" 
    size="50"';
  $form_html .= '</tr>';

  //Password
  $form_html .= '<tr>';
  $form_html .= '<td><th><label for="password">Password:</th></td>';
  $form_html .= '<td><input type="password" name="password" id="password" 
    size="50"';
  $form_html .= '</tr>';
  $form_html .= '</table>';
  $form_html .= '<input type="submit" value="Submit">';
  //TODO: make links with 'forgot password?'
  return $form_html;
}



/**
 * validate_sign_in_form()
 * =======================
 * checks the input from the sign_in POST variables.  If any are blank, 
 * this returns the appropriate errors in the $errors array
 //TODO make this more specific about what the errors will say
*/
function validate_sign_in_form( $vars )
{
  $errors = array();
  return $errors;
}



/**
 * authenticate_user()
 * ===================
 *
 * checks to see if the passed $email and $password are registered in the 
 * database.  If not, then it will return an array with the appropriate 
 * errors.  
 *  //TODO make this more specific about what the errors will say
 *
 * Note: if the user is properly authenticated, this function will set the 
 * $_SESSION['user_id'] variable to the user's database id.
 *  //TODO: Clean this up, this function should not set the variable.  It 
 *  should be left to another function to do
 */
function authenticate_user( $email, $entered_password )
{
  $errors = array();
  $db = new Database_handler();
  $results = $db->query_table(
    'SELECT * FROM t_users WHERE email = "'.$email.'"');

  //check if email exists
  if( sizeof( $results ) == 0 )
  {
    $errors['invalid_email'] = 'Invalid email address';

    return $errors;
  }

  //check if password matches
  $stored_hash = $results[0]['password'];
  $pw_hasher = new PasswordHash(8, false);
  $auth = $pw_hasher->CheckPassword( $entered_password, $stored_hash );
  if( !$auth )
  {
    $errors['invalid_password'] = 'Invalid password';

    return $errors;
  }

  $_SESSION['user_id'] = $results[0]['id'];
  return $errors;
}



//%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%
//%
//% 			                    MAIN CODE
//%
//%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%
if( $_SERVER['REQUEST_METHOD'] == 'GET' )
{
  $body_html = '';

  if( $_SESSION['user_id'] ==  NOT_LOGGED_IN )
  {
    $body_html .= make_sign_in_form();
    echo $body_html;
  }
  else
  {
    //TODO: make something that says "are you sure you want to log out?" 
    //with a button that says confirm, or something
  }
}

elseif( $_SERVER['REQUEST_METHOD'] == 'POST' )
{
    $body_html = '';

    $validation_errors = validate_sign_in_form( $_POST );
    $auth_errors = authenticate_user( $_POST['email'], $_POST['password'] 
    );
    $errors = array_merge( $validation_errors, $auth_errors );

    if( !empty($errors) )
    {
      $body_html .= display_errors( $errors );
      $body_html .= make_sign_in_form();
    }
    else
    {
      //no need to set $_SESSION['user_id'] since authenticate_user() 
      //already did it. TODO: clean this up.  authenticate_user() should 
      //not set that variable
      header( "Location: " . BASE_URL . "index.php" );
    }

    echo $body_html;
}

include( FOOTER_PATH ); 
