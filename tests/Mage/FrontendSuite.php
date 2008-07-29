<?php
class Mage_FrontendSuite extends PHPUnit_Framework_TestSuite
{
    protected function setUp()
    {
        echo "\nFrontend suite setup called - starting Magento.\n";
        Mage::app('admin');
        Mage::getConfig()->init()->loadEventObservers('crontab');
        Mage::app()->addEventArea('crontab');
        Mage::dispatchEvent('default');
    }

    protected function tearDown()
    {
        echo "\nFrontend suite teardown called.\n";
    }
}
