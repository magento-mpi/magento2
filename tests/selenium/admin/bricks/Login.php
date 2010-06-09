<?php

class Login
{
    protected $_object;
    public function  __construct($object = null) {
        $this->_object = $object;
    }
	public function doLogin($baseurl, $username, $password)
	{
            $this->_object->open($baseurl);
            $this->_object->waitForPageToLoad("10000");
            $this->_object->type("username", $username);
            $this->_object->type("login", $password);
            $this->_object->click("//input[@title='Login']");
            $this->_object->waitForPageToLoad("90000");

	}
}