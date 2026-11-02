<?php

namespace App\Controllers;

class Master_user_profile extends BaseController
{
    //Pesanan Aplikasi Page Start Here
    public function profileview()
    {
        $db = db_connect();
        $session = session();
        $DataUser = $db->table("userlogin")->getWhere(['uidlogin' => $session->get('username')])->getRow();
        $css = [
            'public/assets/plugins/bootstrap-fileinput/bootstrap-fileinput.css'
        ];
        $js = [
            'public/assets/plugins/bootstrap-fileinput/bootstrap-fileinput.js'
        ];
        $data  = [
            'css' => $css,
            'js' => $js,
            'nama' => $DataUser->username,
            'email' => $DataUser->email,
            'hp' => $DataUser->mobilephone,
            'website' => $DataUser->website,
            'desc' => $DataUser->descs,
            'avatar' => $DataUser->avatar,
            'cabang' => $session->get('cabang'),
            'title' => 'AJF-Corp',
            'body' => 'admin/master_user/v_master_user_profil',
        ];
        return view('layout/layout', $data);
    }

    public function savebio()
    {
        $session = session();
        $db = db_connect();
        $builder = $db->table('userlogin');
        $input = $this->request->getPost();
        if ($session->get('username') !='') {
            $data = [
                'username' => $input['_nama'],
                'mobilephone' => $input['_nohp'],
                'email' => $input['_email'],
                'website' => $input['_website'],
                'descs' => $input['_desc'],
            ];
            $result = $builder->where('uidlogin', $session->get('username'))->update($data);
            if ($result) {
                echo json_encode('Biodata Berhasil Di update');
            }
        } else {
            echo json_encode('Userid tidak ditemukan ');
        }
    }

    public function uploadImg()
    {
        error_reporting(0);
        $validation = \Config\Services::validation(); 
        $session = session();
        $db = db_connect();
        $builder = $db->table('userlogin');
        $rules = [
            'filefoto' => 'uploaded[filefoto]|max_size[filefoto,1024]|ext_in[filefoto,jpg,jpeg,png,gif]',
        ];
        
        if ($this->validate($rules)) {
            $image = $this->request->getFile('filefoto');

            // Direktori tempat Anda ingin menyimpan gambar
            $uploadPath = WRITEPATHPUSER;

            // Nama file baru dengan timestamp
            $newName = $image->getRandomName();

            // Pindahkan gambar ke direktori yang ditentukan
            $moveImg = $image->move($uploadPath, $newName);

            // Hapus gambar lama jika ada
            $oldImage = $this->request->getPost('filefoto_old');
            if (!empty($oldImage)) {
                unlink($uploadPath . $oldImage);
            }
            if($moveImg == true){
                $builder->where('uidlogin', $session->get('username'))->update(['avatar' => $newName]);
                return $this->profileview();
            }else{
                $this->profileview();
            }
        } else {
            // Tampilkan pesan kesalahan jika validasi gagal
            echo json_encode($validation->listErrors());
        }
    }

    public function Savepw()
	{
        $session = session();
        $db = db_connect();
        $input = $this->request->getPost();
	    $cek = $session->get('level');		
		$uid = $session->get('username');		
		if(!empty($cek))
		{				      
			$passwl = trim($input['_password']);
			$passwb = trim($input['_passwordbaru']);
			
			$query="select pwdlogin as pwd from  userlogin where uidlogin = '$uid'";
			$dat=$db->query($query)->getRow();
			if($dat->pwd != md5($passwl))
			{
			    echo "not ok";
			} else
			{
				$query = "update userlogin set pwdlogin = md5('$passwb') where uidlogin = '$uid'";
				$db->query($query);
				echo "ok";
			}						
		} else
		{
			header('location:'.base_url());
		}
	}	
}