<?php
defined('BASEPATH') OR exit('No direct script access allowed');
//use Suap\Suap_Auth;
class Suap_auth extends CI_Controller {
//https://suap.ifrn.edu.br/o/token/?cliente_id=GwfJKm4db3xi7oyL1KEfmuNi4PUikbY6D453k1er&client_secret=jebonknkdg8Az6vZK0PsajZBKwc49KvSyAgm7kLCRJxAq3BjUKAyuxmvXceODtUoPn5S41FLwREbDuaFwu4Ume9VhNTRC6rtxdgkRAnE7GjgXKssSlYROvvKr7ZxqybJ&redirect_uri=http://localhost/suap_teste/welcome/api/
	/**
	 * Index Page for this controller.
	 *
	 * Maps to the following URL
	 * 		http://example.com/index.php/welcome
	 *	- or -
	 * 		http://example.com/index.php/welcome/index
	 *	- or -
	 * Since this controller is set as the default controller in
	 * config/routes.php, it's displayed at http://example.com/
	 *
	 * So any other public methods not prefixed with an underscore will
	 * map to /index.php/welcome/<method_name>
	 * @see https://codeigniter.com/user_guide/general/urls.html
	 */

	public function __construct(){
		parent::__construct();
		$this->load->library('Suap_OAuth2');
	}
	public function index()
	{
		$suap = $this->suap_oauth2;
		$token = NULL;
		if(isset($_GET['code'])){
			$token = $_GET['code'];
		}
		$suap->init();
		$suap->login($token);
		
	}
}
