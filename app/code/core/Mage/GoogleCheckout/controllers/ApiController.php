<?php

class Mage_GoogleCheckout_ApiController extends Mage_Core_Controller_Front_Action
{
    public function callbackAction()
    {
error_log(__METHOD__."\n", 3, '/tmp/googleckeckout.log');

    }

    public function calculationsAction()
    {
error_log(__METHOD__."\n", 3, '/tmp/googleckeckout.log');
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
error_log(__METHOD__."\n", 3, '/tmp/googleckeckout.log');
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
error_log(__METHOD__."\n", 3, '/tmp/googleckeckout.log');
/*
        $debug = Mage::getModel('googlecheckout/api_debug');
        $debug->setDir('in')
            ->setUrl($this->getRequest()->getPathInfo())
            #->setRequestBody(serialize($this->getRequest()))
            ->save();
*/
    }
}