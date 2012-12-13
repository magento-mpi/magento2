<?php
/**
 * {license_notice}
 *
 * @category    Mage
 * @package     Mage_Core
 * @subpackage  unit_tests
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Test class Mage_Core_Controller_Varien_ActionAbstract
 */
class Mage_Core_Controller_Varien_ActionAbstractTest extends PHPUnit_Framework_TestCase
{
    public function testConstruct()
    {
        $request = $this->getMock('Mage_Core_Controller_Request_Http', array(), array(), '', false);
        $response = $this->getMock('Mage_Core_Controller_Response_Http', array(), array(), '', false);
        $actionAbstract = new Mage_Core_Controller_Varien_Action_Forward($request, $response, 'Area');

        $this->assertAttributeInstanceOf('Mage_Core_Controller_Request_Http', '_request', $actionAbstract);
        $this->assertAttributeInstanceOf('Mage_Core_Controller_Response_Http', '_response', $actionAbstract);
        $this->assertAttributeEquals('Area', '_currentArea', $actionAbstract);
    }
}
