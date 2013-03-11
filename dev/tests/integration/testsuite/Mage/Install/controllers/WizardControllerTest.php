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

class Mage_Install_WizardControllerTest extends Magento_Test_TestCase_ControllerAbstract
{
    public function setUp()
    {
        // emulate non-installed application
        Magento_Test_Helper_Bootstrap::getInstance()->reinitialize(array(
            Mage::PARAM_CUSTOM_LOCAL_CONFIG
                => sprintf(Mage_Core_Model_Config_Primary::CONFIG_TEMPLATE_INSTALL_DATE, 'invalid')
        ));
        parent::setUp();
    }

    public function testPreDispatch()
    {
        $this->dispatch('install/wizard');
        $this->assertEquals(200, $this->getResponse()->getHttpResponseCode());
    }
}
