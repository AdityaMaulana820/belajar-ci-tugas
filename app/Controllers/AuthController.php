<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use App\Models\DiskonModel;
use CodeIgniter\HTTP\ResponseInterface;

use App\Models\UserModel;
 // Tambahan: untuk ambil data diskon



class AuthController extends BaseController
{
    protected $user;

    
    function construct()
    {
        helper('form');
        $this->user= new UserModel();
    }

    public function login()
    {

        if ($this->request->getPost()) {
            $rules = [
                'username' => 'required|min_length[6]',
                'password' => 'required|min_length[7]|numeric',
            ];

            if ($this->validate($rules)) {
                $username = $this->request->getVar('username');
                $password = $this->request->getVar('password');

                $dataUser = $this->user->where(['username' => $username])->first();

                if ($dataUser) {
                    if (password_verify($password, $dataUser['password'])) {

                        // ✅ Set data user ke session
                        session()->set([
                            'username'   => $dataUser['username'],
                            'role'       => $dataUser['role'],
                            'isLoggedIn' => true
                        ]);

                        // ✅ Tambahan fitur nomor 2: Ambil diskon hari ini
                        $tanggalHariIni = date('Y-m-d');
                        $diskonModel = new DiskonModel();
                        $diskonHariIni = $diskonModel->where('tanggal', $tanggalHariIni)->first();

                        if ($diskonHariIni) {
                            session()->set('diskon_nominal', $diskonHariIni['nominal']);
                        }

                        return redirect()->to(base_url());
                    } else {
                        session()->setFlashdata('failed', 'Kombinasi Username & Password Salah');
                        return redirect()->back();
                    }
                } else {
                    session()->setFlashdata('failed', 'Username Tidak Ditemukan');
                    return redirect()->back();
                }
            } else {
                session()->setFlashdata('failed', $this->validator->listErrors());
                return redirect()->back();
            }
        }

        return view('v_login');
    }

    public function logout()
    {
        session()->destroy();
        return redirect()->to('login');
    }
}