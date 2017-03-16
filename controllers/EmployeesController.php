<?php

class EmployeesController extends BaseController {

	/**
	 * Returns the employee or returns 404
	 *
	 * @param $emp_no
	 *
	 * @return mixed
	 */
	private function getEmployeeOrFail( $emp_no ) {
		$emp = Employee::where( 'emp_no', $emp_no )->first();

		if ( ! $emp ) {
			response( 404 );
		}

		return $emp;
	}

	/**
	 * @param Request $request
	 */
	public function index( Request $request ) {
		response( 200, Employee::all()->toArray() );
	}

	/**
	 * @param Request $request
	 * @param $emp_no
	 */
	public function show( Request $request, $emp_no ) {
		$emp = $this->getEmployeeOrFail( $emp_no );
		response( 200, $emp->toArray() );
	}

	/**
	 * @param Request $request
	 * @param $emp_no
	 */
	public function salaries( Request $request, $emp_no ) {
		$this->getEmployeeOrFail( $emp_no );

		$salaries = Salary::where( 'emp_no', $emp_no )->get();

		response( 200, $salaries->toArray() );
	}

	/**
	 * @param Request $request
	 * @param $emp_no
	 */
	public function titles( Request $request, $emp_no ) {
		$this->getEmployeeOrFail( $emp_no );

		$titles = Title::where( 'emp_no', $emp_no )->get();

		response( 200, $titles->toArray() );
	}

	/**
	 * @param Request $request
	 */
	public function create( Request $request ) {
		$data = $request->data;

		try {
			$employee = new Employee( $data );
			$v        = $employee->validate();

			if ( ! $v->validate() ) {
				$errors = $v->errors();

				response( 400, get_first_error( $errors ) );
			}
			if ( array_key_exists( 'emp_no', $request->data ) ) {
				response( 400 );
			}

			$emp_no = $employee->save();

			response( 201, $employee->toArray(), [
				'Location' => $this->newResourceLocation( $request->endpoint, $emp_no )
			] );
		} catch( Exception $e ) {
			response( 500, "The resource could not be created." );
		}
	}


	/**
	 * @param Request $request
	 * @param $emp_no
	 */
	public function put( Request $request, $emp_no ) {
		$emp = $this->getEmployeeOrFail( $emp_no );

		// validate the data.
		$v = $emp->validate( $request->data );
		if ( ! $v->validate() ) {
			$errors = $v->errors();

			response( 400, get_first_error( $errors ) );
		}
		if ( array_key_exists( 'emp_no', $request->data ) ) {
			response( 400 );
		}

		try {
			// update and return resource back with 200;
			$emp->update( $request->data );
			response( 200, $emp->toArray() );
		} catch( Exception $e ) {
			response( 500, "The resource could not be created." );
		}
	}

	/**
	 * @param Request $request
	 * @param $emp_no
	 */
	public function delete( Request $request, $emp_no ) {
		$emp = $this->getEmployeeOrFail( $emp_no );

		try {
			$emp->delete();
			response( 204 );
		} catch( Exception $e ) {
			response( 500, "The resource could not be created." );
		}
	}
}