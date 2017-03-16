<?php
error_reporting( 0 );

define( "API_URL", "Hello world." );
require_once( 'vendor/autoload.php' );
require_once 'database.php';

require_once 'helpers.php';
require_once 'Exceptions.php';
require_once 'Endpoint.php';
require_once 'Request.php';
require_once 'Response.php';
require_once( 'models/Models.php' );

foreach ( glob( "controllers/*.php" ) as $class ) {
	require_once $class;
}


/**
 * Class API
 */
class API {
	public function serve() {
		try {
			// create the request.
			$request = new Request();

			// check api key
			$this->checkApiKey( $request->api_key );

			// check the endpoint.
			$endpoint = new Endpoint( $request->endpoint, $request->method );
			if ( ! $endpoint->isValid() ) {
				response( $endpoint->errorCode() );
			}

			// get the controller, and and call the action with the params sent
			// and return the appropriate response.
			$controller_string = $endpoint->getController();
			$action_string     = $endpoint->getAction();
			$params            = $endpoint->getActionParams();
			array_unshift( $params, $request ); // add the request object as param.

			// check if we can handle this.
			// @todo check if we should throw 400 or other error code,
			// as this would be an error in our configuration array
			if ( ! class_exists( $controller_string ) ) {
				response( 500 );
			}

			$controller = new $controller_string();


			if ( ! method_exists( $controller, $action_string ) ) {
				response( 500 );
			}

			call_user_func_array( array( $controller, $action_string ), $params );

		} catch( InvalidInputException $e ) {
			response( 400 );
		} catch( InvalidMethodException $e ) {
			response( 400 );
		}
	}

	/**
	 * @param $api_key
	 */
	private function checkApiKey( $api_key ) {
		// this could be a MYSQL query that parses an API Key table, for example
		if ( $api_key == '612e648bf9594adb50844cad6895f2cf' ) {
			// authorized
		} else if ( $api_key == null ) {
			// we require authentification, but the api key was not sent, so we
			// have 401 Unauthorized
			response( 401, "Api key not sent", [
				'X-Authenticated' => 'False'
			] );
		} else {
			// the user sent an api key, but it's not a correct one, so we
			// have 403 Forbidden
			response( 403, "Api key invalid", [
				'X-Authenticated' => 'False'
			] );
		}
	}

}