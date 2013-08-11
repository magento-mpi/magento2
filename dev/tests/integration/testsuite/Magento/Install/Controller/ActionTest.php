<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Install
 * @subpackage  integration_tests
 * @copyright   {copyright}
 * @license     {license_link}
 */

class Magento_Install_Controller_ActionTest extends Magento_TestFramework_TestCase_ControllerAbstract
{
    public function testConstruct()
    {
        Mage::getObjectManager()->get('Magento_Core_Controller_Varien_Action_Factory')
            ->createController('Magento_Install_Controller_Action', array('areaCode' => 'frontend'));
        $this->assertEquals('install', Mage::getConfig()->getCurrentAreaCode());
    }
}
