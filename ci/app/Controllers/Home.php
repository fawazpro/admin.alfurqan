<?php

namespace App\Controllers;

class Home extends BaseController
{
	public function index()
	{
		$session = session();
		if ($session->logged_in == TRUE) {
			$session = session();
			if ($session->clearance == 1) {
				return redirect()->to('db');
			}
		} else {
			$this->login();
		}
	}

	public function login()
	{
		echo view('login');
	}

	public function dashboard()
	{
		$session = session();
		if ($session->logged_in == TRUE) {
			echo view('header');
			echo view('sidebar');
			echo view('db');
		} else {
			$this->login();
		}
	}
	public function deletereg($id)
	{
		$session = session();
		if ($session->logged_in == TRUE) {
			$reg = new \App\Models\Reg();
			$db = $reg->delete($id);
			if($db){
				$this->msg(['msg'=>'Successfully Deleted']);
			}
		}else{
			$this->login();
		}
	}

	
	public function msg(array $data)
	{
		echo view('msg', $data);
	}

	public function registrations()
	{
		$session = session();
		if ($session->logged_in == TRUE) {
			$reg = new \App\Models\Reg();
			$db = $reg->findAll();

			$data =  [
				'registrant' => $db,
				'url' => 'http://localhost/alfurqan/dev/imagerender/',
			];
			echo view('header');
			echo view('sidebar');
			echo view('reg', $data);
		} else {
			$this->login();
		}
	}
	
	
	public function preprocess()
	{
			$reg = new \App\Models\Reg();
			$db = $reg->findAll();

			foreach ($db as $key => $one) {
				var_dump(WRITEPATH . 'uploads/' . $one['passport']);
				$image = \Config\Services::image()
					->withFile(WRITEPATH . 'uploads/' . $one['passport'])
					->withResource()
					->save(WRITEPATH.'uploads/'.$one['passport'], 10);
			}
			echo 'done';
			
	}


	public function postlogin()
	{
		$users = new \App\Models\Users();
		$incoming = $this->request->getPost();
		$data = [
			'username' => $incoming['username'],
			// 'pass' => hash('sha256', $incoming['pass']),
			'password' => $incoming['password']
		];
		$result = $users->where($data)->find();
		if ($result) {
			if ($result[0]['clearance'] == 1) {
				$ses_data = [
					'id' => $result[0]['id'],
					'clearance' => $result[0]['clearance'],
					'logged_in' => TRUE,
				];
				$session = session();
				$session->set($ses_data);
				return redirect()->to('db');
			}
		} else {
			echo 'Login not Successful';
		}
	}


	public function logout()
	{
		$session = session();
		$session->destroy();
		return redirect()->to(base_url());
	}

	//--------------------------------------------------------------------

}
