<?php

namespace App\Models;

use CodeIgniter\Model;

class Login_model extends Model
{
    var $details;

    function validate_user( $uid, $password ) {
        $validation =  \Config\Services::validation();
        $db = db_connect();
        $data = [
            'uidlogin' => $uid,
            'pwdlogin' => $password
        ];
        $login = $db->table('userlogin')->where($data);
        $GetRow = $login->get()->getRow();
    
		if ( $GetRow == true) {
			return $GetRow;
		}
		return false;
	}
}
