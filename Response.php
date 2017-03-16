<?php

class Response {

	/**
	 * @var array
	 */
	private $status_codes = [
		'200' => 'Success',
		'201' => 'Created',
		'204' => 'Success',
		'400' => 'Bad Request',
		'401' => 'Unauthorized',
		'403' => 'Forbidden',
		'404' => 'Not found',
		'405' => 'Method not allowed',
	];

	/**
	 * @var
	 */
	private $status_code;

	/**
	 * @var
	 */
	private $data;

	function __construct( $status_code, $data = array(), $headers = array() ) {
		$this->status_code = $status_code;
		$this->data        = $data;
		$this->headers     = $headers;
	}

	/**
	 * Outputs the json response.
	 */
	public function json() {
		http_response_code( $this->status_code );
		header( 'Content-type: application/json; charset=utf-8' );

		// set custom headers.
		if ( $this->headers ) {
			foreach ( $this->headers as $header_key => $header_value ) {
				header( $header_key . ': ' . $header_value );
			}
		}

		if ( substr( $this->status_code, 0, 1 ) === "2" ) {
			echo json_encode( [
				'data' => $this->data
			] );
		} else {
			$errors = [
				'status' => $this->status_code,
				'title'  => $this->status_codes[ $this->status_code ],
			];

			if ( $this->data && is_string( $this->data ) ) {
				$errors['detail'] = $this->data;
			}
			echo json_encode( [
				'errors' => $errors
			] );
		}

	}
}