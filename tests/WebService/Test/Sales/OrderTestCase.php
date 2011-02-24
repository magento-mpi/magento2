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

class WebService_Test_Sales_OrderTestCase extends Mage_TestCase implements PHPUnit_Framework_Test
{
    protected function setUp()
    {
        WebService_Utils_Dispatcher::setModel('Sales/Order');
    }
    
    protected function tearDown()
    {
        WebService_Utils_Dispatcher::setModel(null);
    }

    public function testOrderItem()
    {
        $actualResult   = WebService_Utils_Dispatcher::dispatch('orderList');

        $this->assertTrue($actualResult);
    }

}

