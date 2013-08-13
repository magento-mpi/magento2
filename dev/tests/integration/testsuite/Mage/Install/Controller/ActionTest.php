<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Mage_Install
 * @subpackage  integration_tests
 * @copyright   {copyright}
 * @license     {license_link}
 */

class Mage_Install_Controller_ActionTest extends Magento_Test_TestCase_ControllerAbstract
{
    public function testConstruct()
    {
        Magento_Test_Helper_Bootstrap::getObjectManager()->get('Mage_Core_Controller_Varien_Action_Factory')
            ->createController('Mage_Install_Controller_Action', array('areaCode' => 'frontend'));
        $this->assertEquals('install', Mage::getConfig()->getCurrentAreaCode());
    }
}
