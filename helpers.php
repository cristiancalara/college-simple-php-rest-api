<?php

/**
 * Outputs json for api response
 *
 * @param $status_code
 * @param array $data
 */
function response( $status_code, $data = array(), $headers = array() ) {
	$response = new Response( $status_code, $data, $headers );
	$response->json();
	die();
}

/**
 * Returns first error from Valitron/Validator
 *
 * @param $errors
 *
 * @return mixed
 */
function get_first_error( $errors ) {
	foreach ( $errors as $error ) {
		return $error[0];
	}

	return '';
}