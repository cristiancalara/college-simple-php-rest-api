<?php

/**
 * Class Endpoint
 */
class Endpoint {
	/**
	 * @var int
	 */
	private $error_code;

	/**
	 * @var bool
	 */
	private $valid = false;

	/**
	 * @var mixed
	 */
	private $endpoint;

	/**
	 * @var
	 */
	private $raw_endpoint;

	/**
	 * All accepted endpoints.
	 * @var array
	 */
	private $allowed_endpoints = [
		[
			'method'     => 'GET',
			'route'      => 'employees',
			'controller' => 'EmployeesController',
			'action'     => 'index',
		],
		[
			'method'     => 'POST',
			'route'      => 'employees',
			'controller' => 'EmployeesController',
			'action'     => 'create',
		],
		[
			'method'     => 'GET',
			'route'      => 'employees/{?}',
			'controller' => 'EmployeesController',
			'action'     => 'show',
		],
		[
			'method'     => 'PUT',
			'route'      => 'employees/{?}',
			'controller' => 'EmployeesController',
			'action'     => 'put',
		],
		[
			'method'     => 'DELETE',
			'route'      => 'employees/{?}',
			'controller' => 'EmployeesController',
			'action'     => 'delete',
		],
		[
			'method'     => 'GET',
			'route'      => 'employees/{?}/salaries',
			'controller' => 'EmployeesController',
			'action'     => 'salaries',
		],
		[
			'method'     => 'GET',
			'route'      => 'employees/{?}/titles',
			'controller' => 'EmployeesController',
			'action'     => 'titles',
		],
		[
			'method'     => 'GET',
			'route'      => 'departaments',
			'controller' => 'DepartmentsController',
			'action'     => 'index',
		],
		[
			'method'     => 'GET',
			'route'      => 'departaments/{?}',
			'controller' => 'DepartmentsController',
			'action'     => 'show',
		],
		[
			'method'     => 'GET',
			'route'      => 'departaments/{?}/managers',
			'controller' => 'DepartmentsController',
			'action'     => 'managers',
		],
		[
			'method'     => 'GET',
			'route'      => 'departaments/{?}/employees',
			'controller' => 'DepartmentsController',
			'action'     => 'employees',
		]
	];


	/**
	 * Endpoint constructor.
	 *
	 * @param $endpoint
	 * @param $method
	 */
	function __construct( $endpoint, $method ) {
		$this->valid = false;
		foreach ( $this->allowed_endpoints as $e ) {
			if ( $this->areEqual( $e['route'], $endpoint ) ) {
				if ( $e['method'] == $method ) {
					$this->endpoint = $e; // save the matched endpoint.
					$this->valid    = true;
				} else { // we have this endpoint, but we don't support this method.
					$this->error_code = 405;
				}
			}
		}

		// we don't have this endpoint, so we have a bad request.
		if ( ! $this->valid ) {
			$this->error_code = 400;
		}

		// save for later use.
		$this->raw_endpoint = $endpoint;
	}


	/**
	 * Is endpoint valid?
	 * @return bool
	 */
	public function isValid() {
		return $this->valid;
	}

	/**
	 * @return int
	 */
	public function errorCode() {
		return $this->error_code;
	}


	/**
	 * Returns controller for current endpoint.
	 * @return null
	 */
	public function getController() {
		if ( $this->endpoint ) {
			return $this->endpoint['controller'];
		}

		return null;
	}

	/**
	 * Returns controller action.
	 * @return null
	 */
	public function getAction() {
		if ( $this->endpoint ) {
			return $this->endpoint['action'];
		}

		return null;
	}

	/**
	 * Returns action params, based on the defined route.
	 * @return array
	 */
	public function getActionParams() {
		$params = [];
		if ( $this->endpoint ) {
			$pattern = explode( '/', $this->endpoint['route'] );
			$values  = explode( '/', $this->raw_endpoint );

			// if we reached this point,
			foreach ( $pattern as $i => $p ) {
				if ( $p == '{?}' ) {
					$params[] = isset( $values[ $i ] ) ? $values[ $i ] : null;
				}
			}
		}

		return $params;
	}

	/**
	 * Checks if a raw endpoint matches the pattern one
	 * in the config file.
	 *
	 * @param $pattern_endpoint
	 * @param $raw_endpoint
	 *
	 * @return bool
	 */
	private function areEqual( $pattern_endpoint, $raw_endpoint ) {
		$pattern = explode( '/', $pattern_endpoint );
		$values  = explode( '/', $raw_endpoint );

		if ( count( $pattern ) != count( $values ) ) {
			return false;
		}


		foreach ( $pattern as $i => $p ) {
			if ( $p != '{?}' ) {
				if ( $p != $values[ $i ] ) { // if not wildcard, and doesn't match.
					return false;
				}
			}
		}

		return true;
	}
}