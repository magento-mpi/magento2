<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento
 * @subpackage  integration_tests
 * @copyright   {copyright}
 * @license     {license_link}
 */

class Magento_Test_ResponseTest extends PHPUnit_Framework_TestCase
{
    public function testCanSendHeaders()
    {
        $response = new Magento_TestFramework_Response(
            $this->getMock('Magento_Core_Model_Event_Manager', array(), array(), '', false)
        );
        $this->assertTrue($response->canSendHeaders());
        $this->assertTrue($response->canSendHeaders(false));
    }
}
