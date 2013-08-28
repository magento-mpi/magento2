<?php
/**
 * Integration test for Magento_Core_Model_Validator_Factory
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
class Magento_Core_Model_Validator_FactoryTest extends PHPUnit_Framework_TestCase
{
    /**
     * Test creation of validator config
     *
     * @magentoAppIsolation enabled
     */
    public function testGetValidatorConfig()
    {
        $objectManager = Mage::getObjectManager();
        /** @var Magento_Core_Model_Validator_Factory $factory */
        $factory = $objectManager->get('Magento_Core_Model_Validator_Factory');
        $this->assertInstanceOf('Magento_Validator_Config', $factory->getValidatorConfig());
        // Check that default translator was set
        $translator = Magento_Validator_ValidatorAbstract::getDefaultTranslator();
        $this->assertInstanceOf('Magento_Translate_AdapterInterface', $translator);
        $this->assertEquals('Message', __('Message'));
        $this->assertEquals('Message', $translator->translate('Message'));
        $this->assertEquals(
            'Message with "placeholder one" and "placeholder two"',
            (string)__('Message with "%1" and "%2"', 'placeholder one', 'placeholder two')
        );
    }
}
