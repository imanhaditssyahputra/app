<?php

namespace App\Controllers;

class CommonFun extends BaseController
{
    public function common_edit()
    {
        $id = $this->request->getPost('id');
        $table = $this->request->getPost('table');


        $db = db_connect();
        $builder = $db->table($table);
        $result = $builder->getWhere(['id' => $id])->getRow();

        echo json_encode($result);
    }
    public function common_delete()
    {
        $id = $this->request->getPost('id');
        $table = $this->request->getPost('table');

        $db = db_connect();
        $builder = $db->table($table);
        $delete = $builder->where(['id' => $id])->delete();
        if ($delete) {
            echo json_encode(['message' => 'Deleted']);
        }
    }
}
