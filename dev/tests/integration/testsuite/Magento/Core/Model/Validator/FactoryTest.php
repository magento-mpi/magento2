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
        $objectManager = Magento_TestFramework_Helper_Bootstrap::getObjectManager();
        /** @var Magento_Core_Model_Validator_Factory $factory */
        $factory = $objectManager->get('Magento_Core_Model_Validator_Factory');
        $this->assertInstanceOf('\Magento\Validator\Config', $factory->getValidatorConfig());
        // Check that default translator was set
        $translator = \Magento\Validator\ValidatorAbstract::getDefaultTranslator();
        $this->assertInstanceOf('\Magento\Translate\AdapterInterface', $translator);
        $this->assertEquals('Message', __('Message'));
        $this->assertEquals('Message', $translator->translate('Message'));
        $this->assertEquals(
            'Message with "placeholder one" and "placeholder two"',
            (string)__('Message with "%1" and "%2"', 'placeholder one', 'placeholder two')
        );
    }
}
