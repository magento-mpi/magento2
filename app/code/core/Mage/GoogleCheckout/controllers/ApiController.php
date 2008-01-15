<?php

class Mage_GoogleCheckout_ApiController extends Mage_Core_Controller_Front_Action
{
    public function callbackAction()
    {
error_log(@date('Y-m-d H:i:s').' '.__METHOD__.' REMOTE_IP:'.$_SERVER['REMOTE_ADDR'].' URI:'.$_SERVER['REQUEST_URI'].print_r(file_get_contents('php://input'),1)."\n", 3, '/home/moshe/dev/test/callback.log');


    }

    public function calculationsAction()
    {
error_log(@date('Y-m-d H:i:s').' '.__METHOD__.' REMOTE_IP:'.$_SERVER['REMOTE_ADDR'].' URI:'.$_SERVER['REQUEST_URI'].print_r(file_get_contents('php://input'),1)."\n", 3, '/home/moshe/dev/test/callback.log');


/*
        $debug = Mage::getModel('googlecheckout/api_debug');
        $debug->setDir('in')
            ->setUrl($this->getRequest()->getPathInfo())
            #->setRequestBody(serialize($this->getRequest()))
            ->setResponseBody($this->getRequest()->getHeader('Authorization'))
            ->save();

        $api = Mage::getModel('googlecheckout/api')
            ->processCalculations($this->getRequest());

        $this->getResponse()->setBody($api->getResponse());
*/
    }

    public function notificationsAction()
    {
error_log(@date('Y-m-d H:i:s').' '.__METHOD__.' REMOTE_IP:'.$_SERVER['REMOTE_ADDR'].' URI:'.$_SERVER['REQUEST_URI'].print_r(file_get_contents('php://input'),1)."\n", 3, '/home/moshe/dev/test/callback.log');


/*
        $debug = Mage::getModel('googlecheckout/api_debug');
        $debug->setDir('in')
            ->setUrl($this->getRequest()->getPathInfo())
            #->setRequestBody(serialize($this->getRequest()))
            ->setResponseBody($this->getRequest()->getHeader('Authorization'))
            ->save();

        $api = Mage::getModel('googlecheckout/api')
            ->processNotifications($this->getRequest());

        $this->getResponse()->setBody($api->getResponse());
*/
    }

    public function parameterizedAction()
    {
error_log(@date('Y-m-d H:i:s').' '.__METHOD__.' REMOTE_IP:'.$_SERVER['REMOTE_ADDR'].' URI:'.$_SERVER['REQUEST_URI'].print_r(file_get_contents('php://input'),1)."\n", 3, '/home/moshe/dev/test/callback.log');


/*
        $debug = Mage::getModel('googlecheckout/api_debug');
        $debug->setDir('in')
            ->setUrl($this->getRequest()->getPathInfo())
            #->setRequestBody(serialize($this->getRequest()))
            ->save();
*/
    }
}