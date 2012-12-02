<?php
/**
 * Singly API class
 * 
 * @author Rajiv Tirumalareddy
 * @since 01.12.2012
 * @copyright Rajiv Tirumalareddy - Travu Inc.
 * @version 0.1
 * @license BSD http://www.opensource.org/licenses/bsd-license.php
 */
require('lib/PHP-OAuth2/Client.php');
require('lib/PHP-OAuth2/GrantType/IGrantType.php');
require('lib/PHP-OAuth2/GrantType/AuthorizationCode.php');
require('lib/PHP-OAuth2/GrantType/Password.php');

use \OAuth2\Client as Client;



class Singly extends CApplicationComponent{
	/**
    * The API base URL
    */
	private $AUTHORIZATION_ENDPOINT = 'https://api.singly.com/oauth/authenticate';
	private $TOKEN_ENDPOINT = 'https://api.singly.com/oauth/access_token';

	/**
    * The API CONFIG data set by Yii
    */
	public $CLIENT_ID;
	public $CLIENT_SECRET;
	public $REDIRECT_URI;


	//GETTERS AND SETTERS
	public function setClientId($_CLIENT_ID) {
		$this->CLIENT_ID = $_CLIENT_ID;
	}
	public function setClientSecret($_CLIENT_SECRET) {
		$this->CLIENT_SECRET = $_CLIENT_SECRET;
	}
	public function setRedirectUri($_Redirect_Uri) {
		$this->REDIRECT_URI = $_Redirect_Uri;
	}
	public function getClientId() {
		return $this->CLIENT_ID;
	}
	public function getClientSecret() {
		return $this->CLIENT_SECRET;
	}
	public function getRedirectUri() {
		return $this->REDIRECT_URI;
	}

	private $_singly;

	protected function _getSingly(){
		if (is_null($this->_singly)) {
			if ($this->CLIENT_ID && $this->CLIENT_SECRET) {
				$this->_singly = new Client($this->CLIENT_ID, $this->CLIENT_SECRET);//CHANGE TO GETTER FUNCTIONS
			} else {
				if (!$this->CLIENT_ID)
					throw new CException('Singly client ID not specified.');
				elseif (!$this->CLIENT_SECRET)
					throw new CException('Singly client secret not specified.');
			}
		}
		if(!is_object($this->_singly)) {
			throw new CException('Singly API could not be initialized.');
		}
		return $this->_singly;
	}

	public function getSinglyAuthenticationUrl($service) {
		$auth_url = $this->_getSingly()->getAuthenticationUrl($this->AUTHORIZATION_ENDPOINT, $this->getRedirectUri())."&service=". $service;
			if (isset(Yii::app()->session['singly_token'])) {
				$auth_url .= '&access_token=' . Yii::app()->session['singly_token'];
			}
			return $auth_url;
	}

	public function setAccessToken($code){
		$params = array('code' => $code, 'redirect_uri' => $this->getRedirectUri());
		$response = $this->_getSingly()->getAccessToken($this->TOKEN_ENDPOINT, 'authorization_code', $params);
		if($response['result']['access_token']){
			Yii::app()->session['singly_token'] = $response['result']['access_token'];
			return true;
		}
		else{
			return false;
		}

	}

	/**
    * Return's Unique User Token
    */
	public function getUser(){
		if(isset(Yii::app()->session['singly_token'])){
			$this->_getSingly()->setAccessToken(Yii::app()->session['singly_token']);
			$result = $this->_getSingly()->fetch('https://api.singly.com/profiles');
			return $result['result']['id'];
		}
	}


	/**
    * Make Singly API Calls
    */
	public function fetch($apicall = '/profiles', $params=null, $method = 'GET'){
		$this->_getSingly()->setAccessToken(Yii::app()->session['singly_token']);
		return $this->_getSingly()->fetch('https://api.singly.com'.$apicall, $params, $method);
	}

	public function removeAccessToken(){
		unset(Yii::app()->session['singly_token']);
	}
}
?>