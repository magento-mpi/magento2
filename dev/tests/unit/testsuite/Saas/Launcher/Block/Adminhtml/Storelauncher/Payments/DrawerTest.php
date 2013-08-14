<?php
/**
 * {license_notice}
 *
 * @category    Mage
 * @package     Saas_Launcher
 * @subpackage  unit_tests
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Test class for Saas_Launcher_Block_Adminhtml_Storelauncher_Payments_Drawer
 */
class Saas_Launcher_Block_Adminhtml_Storelauncher_Payments_DrawerTest extends PHPUnit_Framework_TestCase
{
    public function testGetMoreUrl()
    {
        $objectManagerHelper = new Magento_Test_Helper_ObjectManager($this);

        $configGroup = $this->getMock('Magento_Backend_Model_Config_Structure_Element_Group', array(), array(), '', false);
        $configGroup->expects($this->once())
            ->method('getAttribute')
            ->with('more_url')
            ->will($this->returnValue('http://more/url'));

        $configStructure = $this->getMock('Magento_Backend_Model_Config_Structure', array(), array(), '', false);
        $configStructure->expects($this->once())
            ->method('getElement')
            ->with('payment/paypal_payments/wpp')
            ->will($this->returnValue($configGroup));

        $arguments = array(
            'configStructure' => $configStructure,
        );

        /** @var Saas_Launcher_Block_Adminhtml_Storelauncher_Payments_Drawer $block */
        $block = $objectManagerHelper->getObject(
            'Saas_Launcher_Block_Adminhtml_Storelauncher_Payments_Drawer',
            $arguments
        );

        $this->assertEquals('http://more/url', $block->getMoreUrl('payment/paypal_payments/wpp'));
    }
}
