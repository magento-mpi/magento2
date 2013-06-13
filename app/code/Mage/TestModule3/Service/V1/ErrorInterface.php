<?php

interface Mage_TestModule2_Service_V1_ErrorInterface
{
    public function success($request);
    public function resourceNotFoundException($request);
    public function serviceException($request);
    public function authorizationException($request);
    public function otherException($request);
}
