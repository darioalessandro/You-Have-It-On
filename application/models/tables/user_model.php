<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class User_model extends Table_model{
	const cId = "id";
	const cName = "name";
	const cLastName = "last_name";
	const cEmail = "email";
	const cPassword = "password";
	const cBirthday = "birthday";
	const cSex = "sex";
	const cConfirmed = "confirmed";
	const cTokenGoogle = "token_google";
	const cTokenFacebook = "token_facebook";
	
	
	protected function getTableName(){
		return "users";
	}
	
	
	public function getId(){
		return $this->campo(self::cId);
	}
	
	
	public function setId($id){
		$this->campo(self::cId, $id);
	}
	
	
	public function getName(){
		return $this->campo(self::cName);
	}
	
	
	public function setName($name){
		$this->campo(self::cName, $name);
	}
	
	
	public function getLastName(){
		return $this->campo(self::cLastName);
	}
	
	
	public function setLastName($last_name){
		$this->campo(self::cLastName, $last_name);
	}
	
	
	public function getEmail(){
		return $this->campo(self::cEmail);
	}
	
	
	public function setEmail($email){
		$this->campo(self::cEmail, $email);
	}
	
	
	public function getPassword(){
		return $this->campo(self::cPassword);
	}
	
	
	public function setPassword($password){
		$this->campo(self::cPassword, $password);
	}
	
	
	public function getBirthday(){
		return $this->campo(self::cBirthday);
	}
	
	
	public function setBirthday($birthday){
		$this->campo(self::cBirthday, $birthday);
	}
	
	
	public function getSex(){
		return $this->campo(self::cSex);
	}
	
	
	/**
	 * m: Male
	 * f: Female
	 * Default: m
	 * @param char $sex <m, f>
	 */
	public function setSex($sex){
		$this->campo(self::cSex, $sex);
	}
	
	
	public function getConfirmed(){
		return $this->campo(self::cConfirmed);
	}
	
	
	public function setConfirmed($confirmed){
		$this->campo(self::cConfirmed, $confirmed);
	}
	
	
	public function getTokenFacebook(){
		return $this->campo(self::cTokenFacebook);
	}
	
	
	public function setTokenFacebook($tokenFacebook){
		$this->campo(self::cTokenFacebook, $tokenFacebook);
	}
	
	
	public function getTokenGoogle(){
		return $this->campo(self::cTokenGoogle);
	}
	
	
	public function setTokenGoogle($tokenGoogle){
		$this->campo(self::cTokenGoogle, $tokenGoogle);
	}
	
	
	/**
	 * Configuramos los campos que sus propiedades cumplen con:
	 * 	-esLLaveForanea = true
	 * 	-esAutoIncrementable = true
	 * 	-esUnico = true
	 * 	-tienen defaultValue
	 * 	-esNullable = true
	 */
	protected function configCampos(){
		//Id
		$this->campos[self::cId]->setEsAutoIncrementable(true);
		
		//Last Name
		$this->campos[self::cLastName]->setEsNullable(true);
		$this->campos[self::cLastName]->setDefault(MetaCampo::DEFAULT_NULL);
		
		//Birthday
		$this->campos[self::cBirthday]->setEsNullable(true);
		$this->campos[self::cBirthday]->setDefault(MetaCampo::DEFAULT_CURRENT_TIMESTAMP);
		
		//Sex
		$this->campos[self::cSex]->setEsNullable(true);
		$this->campos[self::cSex]->setDefault("m");
		
		//Confirmed
		$this->campos[self::cConfirmed]->setDefault(0);
		
		//Token Google
		$this->campos[self::cTokenGoogle]->setEsNullable(true);
		
		//Token Facebook
		$this->campos[self::cTokenFacebook]->setEsNullable(true);
	}	
}