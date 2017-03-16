<?php

class Department extends Illuminate\Database\Eloquent\Model {

}

class Employee extends Illuminate\Database\Eloquent\Model {
	protected $primaryKey = 'emp_no';

	protected $fillable = [ 'birth_date', 'first_name', 'last_name', 'gender', 'hire_date' ];

	public $timestamps = false;

	/**
	 * @var array
	 */
	private $rules = [
		'required'   => [
			[ 'birth_date' ],
			[ 'first_name' ],
			[ 'last_name' ],
			[ 'gender' ],
			[ 'hire_date' ]
		],
		'in'         => [
			[ 'gender', [ 'M', 'F' ] ]
		],
		'lengthMax'  => [
			[ 'first_name', 14 ],
			[ 'last_name', 16 ]
		],
		'dateFormat' => [
			[ 'birth_date', 'Y-m-d' ],
			[ 'hire_date', 'Y-m-d' ],
		]
	];

	/**
	 * @param $data
	 */
	public function validate( $data = array() ) {
		$data = $data ? $data : $this->attributes;
		$v    = new \Valitron\Validator( $data );
		$v->rules( $this->rules );

		return $v;
	}
}

class Salary extends Illuminate\Database\Eloquent\Model {
}

class Title extends Illuminate\Database\Eloquent\Model {
}

class DeptManager extends Illuminate\Database\Eloquent\Model {
}

class DeptEmp extends Illuminate\Database\Eloquent\Model {
}

