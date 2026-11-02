<?php

namespace App\Models;

use CodeIgniter\Model;

class Commom_modal extends Model
{
    public function data_edit($id, $table)
    {
        $db = db_connect();
        $row = $db->table($table)->where('kode', $id)->get()->getRow();
        echo json_encode($row);
    }
}
