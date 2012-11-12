<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Mage_Core
 * @subpackage  integration_tests
 * @copyright   {copyright}
 * @license     {license_link}
 */

class Mage_Core_Model_ValidatorFactoryTest extends PHPUnit_Framework_TestCase
{
    /**
     * Test create of validator config
     *
     * @magentoAppIsolation enabled
     */
    public function testCreate()
    {
        $objectManager = Mage::getObjectManager();
        /** @var Mage_Core_Model_ValidatorFactory $factory */
        $factory = $objectManager->get('Mage_Core_Model_ValidatorFactory');
        $this->assertInstanceOf('Magento_Validator_Config', $factory->create());
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
