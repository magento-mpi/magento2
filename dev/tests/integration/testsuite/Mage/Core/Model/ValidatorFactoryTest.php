<?php
/**
 * Integration test for Mage_Core_Model_ValidatorFactory
 *
 * @copyright {}
 */
class Mage_Core_Model_ValidatorFactoryTest extends PHPUnit_Framework_TestCase
{
    /**
     * Test creation of validator config
     *
     * @magentoAppIsolation enabled
     */
    public function testCreateValidatorConfig()
    {
        $objectManager = Mage::getObjectManager();
        /** @var Mage_Core_Model_ValidatorFactory $factory */
        $factory = $objectManager->get('Mage_Core_Model_ValidatorFactory');
        $this->assertInstanceOf('Magento_Validator_Config', $factory->createValidatorConfig());
        // Check that default translator was set
        $translator = Magento_Validator_ValidatorAbstract::getDefaultTranslator();
        $this->assertInstanceOf('Magento_Translate_AdapterInterface', $translator);
        $this->assertEquals('Message', $translator->__('Message'));
        $this->assertEquals('Message', $translator->translate('Message'));
        $this->assertEquals(
            'Message with "placeholder one" and "placeholder two"',
            $translator->__('Message with "%s" and "%s"', 'placeholder one', 'placeholder two')
        );
    }
}
