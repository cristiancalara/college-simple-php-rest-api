<?php

class Request {

	/**
	 * Used to check for a bad request.
	 * @var bool
	 */
	private $valid = true;

	/**
	 * @var string
	 */
	public $endpoint;


	/**
	 * Request constructor.
	 */
	function __construct() {
		$this->endpoint = $this->getEndpoint();

		$this->method = $_SERVER['REQUEST_METHOD'];
		if ( $this->method == 'POST' && array_key_exists( 'HTTP_X_HTTP_METHOD', $_SERVER ) ) {
			if ( $_SERVER['HTTP_X_HTTP_METHOD'] == 'DELETE' ) {
				$this->method = 'DELETE';
			} else if ( $_SERVER['HTTP_X_HTTP_METHOD'] == 'PUT' ) {
				$this->method = 'PUT';
			} else {
				$this->valid = false;
			}
		}

		if ( $this->valid ) {
			$input = file_get_contents( 'php://input' );
			switch ( $this->method ) {
				case 'DELETE':
				case 'POST':
				case 'PUT':
					if ( $input ) {
						$input = json_decode( $input, true );
						if ( json_last_error() !== 0 ) {
							throw new InvalidInputException();
						}
						$this->data = $input;
					}

					break;
				case 'GET':
					$this->data = $this->cleanInputs( $_GET );
					break;
				default:
					throw new InvalidMethodException();
					break;
			}
		}

		$this->api_key = $_SERVER['HTTP_X_API_KEY'];
	}

	/**
	 * @return bool
	 */
	public function isValid() {
		return $this->valid;
	}

	/**
	 * @return string
	 */
	private function getEndpoint() {
		$endpoint = '/';
		if ( ! empty( $_SERVER['PATH_INFO'] ) ) {
			$endpoint = $_SERVER['PATH_INFO'];
		} else {
			if ( ! empty( $_SERVER['REQUEST_URI'] ) ) {
				if ( strpos( $_SERVER['REQUEST_URI'], '?' ) > 0 ) {
					$endpoint = strstr( $_SERVER['REQUEST_URI'], '?', true );
				} else {
					$endpoint = $_SERVER['REQUEST_URI'];
				}
			}
		}

		if ( $endpoint != '/' ) { // if we have more than "/", we trim "/" on both ends.
			$endpoint = ltrim( $endpoint, '/' );
			$endpoint = rtrim( $endpoint, '/' );
		}

		return $endpoint;
	}

	/**
	 * @param $data
	 *
	 * @return array|string
	 */
	private function cleanInputs( $data ) {
		$clean_input = array();
		if ( is_array( $data ) ) {
			foreach ( $data as $k => $v ) {
				$clean_input[ $k ] = $this->cleanInputs( $v );
			}
		} else {
			$clean_input = trim( strip_tags( $data ) );
		}

		return $clean_input;
	}
}