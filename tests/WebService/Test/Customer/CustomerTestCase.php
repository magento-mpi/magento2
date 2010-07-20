<?php
/* 
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of CustomerTest
 *
 * @author Vladimir
 */

class WebService_Test_Customer_CustomerTestCase extends Mage_TestCase implements PHPUnit_Framework_Test
{
    protected function setUp()
    {
        WebService_Utils_Dispatcher::setModel('Customer/Customer');
    }
    
    protected function tearDown()
    {
        WebService_Utils_Dispatcher::setModel(null);
    }
    
    public function testCreate()
    {
        $expectedResult = WebService_Utils_Dispatcher::dispatch('customerCreate', 'Xml');
        $actualResult   = WebService_Utils_Dispatcher::dispatch('customerCreate');
        
        $this->assertEquals($expectedResult, $actualResult);
    }

    public function testList()
    {
        $expectedResult = WebService_Utils_Dispatcher::dispatch('customerList', 'Xml');
        $actualResult   = WebService_Utils_Dispatcher::dispatch('customerList');

        $this->assertEquals($expectedResult, $actualResult);
    }

    public function testInfo()
    {
        $expectedResult = WebService_Utils_Dispatcher::dispatch('customerInfo', 'Xml');
        $actualResult   = WebService_Utils_Dispatcher::dispatch('customerInfo');

        $this->assertEquals($expectedResult, $actualResult);
    }

    public function testUpdate()
    {
        $expectedResult = WebService_Utils_Dispatcher::dispatch('customerUpdate', 'Xml');
        $actualResult   = WebService_Utils_Dispatcher::dispatch('customerUpdate');

        $this->assertEquals($expectedResult, $actualResult);
    }

    public function testDelete()
    {
        $expectedResult = WebService_Utils_Dispatcher::dispatch('customerDelete', 'Xml');
        $actualResult   = WebService_Utils_Dispatcher::dispatch('customerDelete');

        $this->assertEquals($expectedResult, $actualResult);
    }

}

