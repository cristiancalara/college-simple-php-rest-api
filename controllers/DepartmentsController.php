<?php

use Illuminate\Database\Capsule\Manager as Capsule;

class DepartmentsController extends BaseController {

	/**
	 * Returns the employee or returns 404
	 *
	 * @param $emp_no
	 *
	 * @return mixed
	 */
	private function getDepartmentOrFail( $dept_no ) {
		$dept = Department::where( 'dept_no', $dept_no )->first();

		if ( ! $dept ) {
			response( 404 );
		}

		return $dept;
	}


	/**
	 * @param Request $request
	 */
	public function index( Request $request ) {
		response( 200, Department::all()->toArray() );
	}

	/**
	 * @param Request $request
	 * @param $dept_no
	 */
	public function show( Request $request, $dept_no ) {
		$dept = $this->getDepartmentOrFail( $dept_no );

		response( 200, $dept->toArray() );
	}

	/**
	 * @param Request $request
	 * @param $dept_no
	 */
	public function employees( Request $request, $dept_no ) {
		$this->getDepartmentOrFail( $dept_no );

		$employees = Capsule::table( 'dept_emp' )
		                    ->join( 'employees', 'dept_emp.emp_no', '=', 'employees.emp_no' )
		                    ->select( 'employees.*' )
		                    ->where( 'dept_emp.dept_no', $dept_no )
		                    ->get();

		response( 200, $employees->toArray() );
	}

	/**
	 * @param Request $request
	 * @param $dept_no
	 */
	public function managers( Request $request, $dept_no ) {
		$this->getDepartmentOrFail( $dept_no );

		$managers = Capsule::table( 'dept_manager' )
		                   ->join( 'employees', 'dept_manager.emp_no', '=', 'employees.emp_no' )
		                   ->select( 'employees.*' )
		                   ->where( 'dept_manager.dept_no', $dept_no )
		                   ->get();

		response( 200, $managers->toArray() );
	}
}