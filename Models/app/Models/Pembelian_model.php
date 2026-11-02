<?php

namespace App\Models;

use CodeIgniter\Model;

class Pembelian_model extends Model
{
    public function suplier_add_model()
    {
        $db = db_connect();
        $input = $this->request->getPost();
        $data = [
            'kode' => $input['kode']
        ];
    }
}
