<?php
interface WebService_Connector_Interface
{
    public function init($url);
    public function startSession($apiLogin, $apiPassword);
    public function endSession();
    public function call($method, $params);
    public function multiCall($methods);
}