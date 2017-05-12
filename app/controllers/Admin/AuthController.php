<?php namespace Admin;

class AuthController extends \BaseController {

	/**
	 * Display login form
	 * @return  response
	 */
	public function login() {
		return \Response::view('admin.login');
	}

	/**
	 * Try to authenticate
	 * @return  response
	 */
	public function postLogin() {
		if (\Auth::attempt(array('username' => \Input::get('username'), 'password' => \Input::get('password'))))
		{
    		return \Redirect::intended('admin/');
		}
		else {
			return \Redirect::back()->withErrors(['username' => 'Usuário ou senha inválido']);
		}
	}

	/**
	 * Make logout and redirect to login page
	 * @return  response
	 */
	public function logout() {
		if (\Auth::logout()) {
			return \Redirect::route('admin.login');
		}
	}
}
