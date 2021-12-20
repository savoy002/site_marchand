<?php

namespace App\Entity;


class TypeAccess 
{

	private const TYPE_ACCESS = 
	[
		'verifiy_user' => 'VERIFY_USER',
		'forgot_password' => 'FORGOT_PASSWORD'
	];

	public function getListTypeAccess()
	{
		$types_access = array();
		foreach(self::TYPE_ACCESS as $key => $value)
			$types_access[$key] = $value;
		return $types_access;
	}

}
