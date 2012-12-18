<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Mage_DesignEditor
 * @subpackage  unit_tests
 * @copyright   {copyright}
 * @license     {license_link}
 */

class Mage_DesignEditor_Model_Url_NavigationModeTest extends PHPUnit_Framework_TestCase
{
    /**#@+
     * Test route params
     */
    const FRONT_NAME = 'vde';
    const ROUTE_PATH = 'design';
    /**#@-*/

    public function testGetRoutePath()
    {
        $helper = $this->getMock('Mage_DesignEditor_Helper_Data', array(), array(), '', false);
        $helper->expects($this->once())
            ->method('getFrontName')
            ->will($this->returnValue(self::FRONT_NAME));

        $urlModel = new Mage_DesignEditor_Model_Url_NavigationMode($helper, array('route_path' => self::ROUTE_PATH));
        $this->assertEquals(self::FRONT_NAME . '/' . self::ROUTE_PATH, $urlModel->getRoutePath());
    }
}
