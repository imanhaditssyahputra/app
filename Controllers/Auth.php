<?php

/**
 * @author : Iman Haditssyahputra
 * @web : https://imanhaditssyahputra.site
 * @keterangan : Controller untuk modul Auth
 **/

namespace App\Controllers;

use App\Models\Login_model;

class Auth extends BaseController
{
    public function index()
    {
		$model_Global =  model('Global_model', 'M_GLOBAL');
        $DataIdentity = $model_Global->_LoadProfile();
        if (!empty(session()->get('username'))) {
            return $this->response->redirect(base_url('/dashboard'));
        } else {
			($DataIdentity->active_website == 1) ? $viewlink = 'errors/html/maintenance' : $viewlink = 'admin/login';
            $css = [];
            $js = [];
            $data  = [
                'css' => $css,
                'js' => $js,
                'title' => 'AJF-Corp',
            ];
            return view($viewlink, $data);
        }
    }

    public function login_check()
    {
        $input = $this->request->getPost();
        $model_login =  new Login_model();
        $validation =  \Config\Services::validation();
        $db = db_connect();
        $dataLogin = $model_login->validate_user($input['username'], md5($input['password']));

        if ($dataLogin == true) {
            $data_sess = [
                'username' => $dataLogin->uidlogin,
                'is_logged_in' => true,
                'nama_lengkap' => $dataLogin->username,
                'photo' => $dataLogin->avatar,
                'level' => $dataLogin->level,
                'cabang' => $dataLogin->cabang,
                'debug' => $dataLogin->debug,
            ];
            $session = \Config\Services::session();
            $session->set($data_sess);
            $request = service('request');
            $userIP = $request->getIPAddress();
            $builder = $db->table("ci_sessions");
            $data = [
                'session_id' => $dataLogin->uidlogin,
                'ip_address' => $userIP,
                'user_agent' => $dataLogin->uidlogin,
                'last_activity' => date('Y-m-d H:i:s'),
                'user_data' => $dataLogin->uidlogin,
            ];
            $RowData = $builder->getWhere(['user_data' => $dataLogin->uidlogin])->getRow();
            if ($RowData != '') {
                $builder->where(['user_data' => $dataLogin->uidlogin])->update(['ip_address' => $userIP, 'last_activity' => date('Y-m-d H:i:s')]);
            } else {
                $builder->insert($data);
            }

            return $this->response->redirect(base_url('/dashboard'));
        } else {
            $session = session();
            $session->setFlashdata('ntf1', 'Username atau Password yang dimasukan salah, Silahkan coba lagi, hubungi administrator untuk mendaftarkan user dan password aplikasi');
            return $this->response->redirect(base_url('/'));
        }
    }

    public function log_out()
    {
        session()->remove(['username', 'password', 'is_logged_in']);
        session()->destroy();
        return $this->response->redirect(base_url('/'));
    }

    public function getcabang($uid)
    {
        $db = db_connect();
        $user = $db->query("select ms_unit.nama as namacabang, ms_unit.kode
		from userlogin inner join ms_unit on userlogin.cabang=ms_unit.kode
		where userlogin.uidlogin = '$uid'")->getRow();
        // $db = db_connect();
        // $res = $db->table('inv_gudang')->get()->getResult();
        if ($user) {
            $data = $user;
        } else {
            $data = [
                'namacabang'    => '***',

            ];
        }

        echo json_encode($data);
    }

    function selectcabang($str)
    {
        $input = $this->request->getPost();
        $q = $input['searchTerm'];
        $db = db_connect();
        $query = '';
        if ($q != "") {
            $query = $db->query("select kode as id, nama as text from ms_unit where nama like '%$str%' ");
        } else {
            $query = $db->query("select kode as id, nama as text from ms_unit limit 5");
        }

        return $query->getResult();
    }
	
	public function Active()
    {
        $model_Global =  model('Global_model', 'M_GLOBAL');
        $DataIdentity = $model_Global->_LoadProfile();
        $css = [];
        $js = [];
        $data  = [
            'css' => $css,
            'js' => $js,
            'title' => 'AJF-Corp',
            'DataIdentity' => $DataIdentity,
        ];
        return view('admin/ActiveWeb', $data);
    }

    public function ActiveCheck($value)
    {
        $db = db_connect();
        $model_Global =  model('Global_model', 'M_GLOBAL');
        $DataIdentity = $model_Global->_LoadProfile();

        ($DataIdentity->active_website == 0) ? $Checked = 1 : $Checked = 0;
        $builder = $db->table('ms_identity');
        $result = $builder->where('kodecbg', '01')->update(['active_website' => $Checked]);
    }
}
