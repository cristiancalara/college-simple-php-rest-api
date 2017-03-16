<?php

class BaseController {
	const BASE_URL = "http://demo-api.local:8080/";

	/**
	 * @param $endpoint
	 * @param $identifier
	 *
	 * @return string
	 */
	protected function newResourceLocation( $endpoint, $identifier ) {
		return self::BASE_URL . $endpoint . '/' . $identifier;
	}
}