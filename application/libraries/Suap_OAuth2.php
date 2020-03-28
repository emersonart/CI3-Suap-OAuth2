<?php
//namespace Suap;
/**
 * Classe que representa um token de autorização.
 *
 * @constructor
 *
 * @param {string} token - A sequência de caracteres que representa o Token.
 * @param {number} expirationTime - Número de segundos que o token durará.
 * @param {string} scope - A lista de escopos (separados por espaço) que foi autorizado pelo usuário.
*/
class Suap_OAuth2{
	var $CI;
	
	protected $config;

  protected $token;

  protected $client_id;

  protected $data_json;

  protected $login_url;

  protected $client_secret;

  protected $scope;

  protected $access_token;

  public $token_expires;

  protected $data;

  protected $token_type;

	protected $refresh_token;

	protected $params;

	/*
	 *
	 * 
	 * 
	 *	DEFAULTS 
	 * 
	 * 
	 *
	*/
	protected $cookie_name;

	protected $suap_url;

	protected $resource_url;

  protected $authorization_url;

  protected $logout_url;

  protected $token_url;

	protected $redirect_uri;
	
  protected $response_type;

	protected $grant_type;

	protected $POST = 'POST';

	protected $GET = 'GET';




  public function __construct(){
    //setando o id do cliente
		$this->CI =&get_instance();

		$this->CI->config->load('suap_auth',TRUE);

		$this->config = $this->CI->config->item('suap_auth');

		$this->client_id = $this->config['client_id'];

		$this->client_secret = $this->config['client_secret'];

		$this->cookie_name = (isset($this->config['cookie_name']) ?$this->config['cookie_name'] : 'SUAP_AUTH_eth');

		$this->resource_url = (isset($this->config['resource_url']) ? $this->config['resource_url'] : 'https://suap.ifrn.edu.br/api/eu/');

		$this->authorization_url = (isset($this->config['authorization_url']) ? $this->config['authorization_url'] : 'https://suap.ifrn.edu.br/o/authorize/');

		$this->suap_url = (isset($this->config['suap_url']) ? $this->config['suap_url'] : 'https://suap.ifrn.edu.br/');

		$this->response_type = (isset($this->config['response_type']) ? $this->config['response_type'] : 'code');

		$this->logout_url = (isset($this->config['logout_url']) ? $this->config['logout_url'] : 'https://suap.ifrn.edu.br/o/revoke_token/');
		
		$this->token_url = (isset($this->config['token_url']) ? $this->config['token_url'] : 'https://suap.ifrn.edu.br/o/token/');
	
	
	}

  public function init($config = NULL){

		$this->client_id = (isset($config['client_id']) ? $config['client_id'] : $this->config['client_id']);
		
		$this->client_secret = (isset($config['client_secret']) ? $config['client_secret'] : $this->config['client_secret']);
		
		$this->set_token((isset($config['code']) ? $config['code'] : NULL));

		$this->redirect_uri = (isset($config['redirect_uri']) ? $config['redirect_uri'] : base_url($this->config['redirect_uri']));

		$this->grant_type = (isset($config['grant_type']) ? $config['grant_type'] : $this->config['grant_type']);

		
    

    $this->set_login_url();

    //$this->login($this->token);
	}
	
	public function set_token($token){

		$this->token = $token != '' ? $token : null;
		
    return true;
	}
	
	private function set_login_url(){

    $this->login_url = $this->authorization_url.
      "?response_type=".$this->response_type.
      "&grant_type=".$this->grant_type.
      "&client_id=".$this->client_id.
			"&redirect_uri=".$this->redirect_uri.
			'&scopes=informacao+email+dados';
			
		return $this->login_url;

  }

  public function login($token = NULL){
		if(!$token){
		 	header('Location: '.$this->login_url);
		}
		$this->set_token($token);
    
    $params =  [
			'grant_type'=>str_replace('-','_',$this->grant_type),
			'code'=>$this->token,
			'client_id'=>$this->client_id,
			'client_secret'=>$this->client_secret,
			'redirect_uri'=>$this->redirect_uri
		];
		$result = $this->send_request($this->token_url,$params,NULL,$this->POST);

		if(!$this->is_authenticated()){
			if(isset($result['access_token'])){
				$this->access_token = $result['access_token'];
				$this->token_expires = $result['expires_in'];
				$this->token_type = $result['token_type'];
				$this->scope = explode(' ',$result['scope']);
				$this->refresh_token = $result['refresh_token'];
				
				$this->set_cookie($result);
	
				
			}
		}

		$this->set_data();
    
	}

	public function is_authenticated(){
		return $this->is_valid();
	}

	private function is_valid(){
		if($this->get_cookie()){
			return true;
		}
		return false;
	}

	private function set_cookie($data){
		set_cookie($this->cookie_name,serialize($data),$data['expires_in']);
		return true;
	}

	public function get_cookie($key = NULL){
		$cookie = unserialize(get_cookie($this->cookie_name));
		if($key){
			$cookie = $cookie[$key];
		}
		return $cookie;
	}

	public function unset_cookie(){
		delete_cookie($this->cookie_name);
		return true;
	}

 

  private function set_data(){
		$headers = [
			'Authorization: '.$this->token_type." ".$this->access_token,
		];
    $this->data = $this->send_request($this->resource_url,NULL,$headers);
    return true;
	}
	
  

	public function refresh_token($token = NULL){

		$headers = [
			'Authorization: '.$this->token_type." ".$this->access_token,
		];

		$url = $this->token_url;

		$params = [
			'grant_type' => 'refresh_token',
			'refresh_token' => ($token ? $token : $this->refresh_token),
			'client_id' => $this->client_id,
			'client_secret' => $this->client_secret,
		];

		$method = 'POST';
		$result = $this->send_request($url,$params,$headers,$method);

		if(isset($result['access_token'])){
      $this->access_token = $result['access_token'];
      $this->token_expires = $result['expires_in'];
      $this->token_type = $result['token_type'];
      $this->scope = explode(' ',$result['scope']);
			$this->refresh_token = $result['refresh_token'];
			
			$this->set_cookie($result);

      $this->set_data();
    }
		return true;

	}

	private function send_request($url, $params=NULL, $headers=[],$method = 'GET'){


    $ch = curl_init();
	
		if($params){
			if(is_array($params)){
				if($method == 'POST'){
					curl_setopt($ch, CURLOPT_POST, 1);
    			curl_setopt($ch, CURLOPT_POSTFIELDS,$params);
				}else{
					$url = $url."?".http_build_query($params);
				}
			}else{
				$url = $url."?".$params;
			}
		}
		curl_setopt($ch,CURLOPT_URL,$url);

		if($headers){
			$headers = [
				'Authorization: '.$this->token_type." ".$this->access_token,
			];
			curl_setopt($ch, CURLOPT_HTTPHEADER,$headers);
		}
    
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);  
    $result = curl_exec($ch);

    curl_close($ch);

    $result = json_decode($result,true);
    return $result;
  }



	

	public function get_access_token(){
		return $this->access_token;
	}
  public function get_data(){
    return $this->data;
  }
  public function get_escope(){
    return $this->scope;
  }
  public function get_client(){
    return $this->client_id;
  }

  public function get_login_url(){
    return $this->login_url;
  }
}
