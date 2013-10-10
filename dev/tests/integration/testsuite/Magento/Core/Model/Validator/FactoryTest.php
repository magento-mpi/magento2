<?php
/**
 * Integration test for \Magento\Core\Model\Validator\Factory
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Core\Model\Validator;

class FactoryTest extends \PHPUnit_Framework_TestCase
{
    /**
     * Test creation of validator config
     *
     * @magentoAppIsolation enabled
     */
    public function testGetValidatorConfig()
    {
        $objectManager = \Magento\TestFramework\Helper\Bootstrap::getObjectManager();
        /** @var \Magento\Core\Model\Validator\Factory $factory */
        $factory = $objectManager->get('Magento\Core\Model\Validator\Factory');
        $this->assertInstanceOf('Magento\Validator\Config', $factory->getValidatorConfig());
        // Check that default translator was set
        $translator = \Magento\Validator\AbstractValidator::getDefaultTranslator();
        $this->assertInstanceOf('Magento\Translate\AdapterInterface', $translator);
        $this->assertEquals('Message', __('Message'));
        $this->assertEquals('Message', $translator->translate('Message'));
        $this->assertEquals(
            'Message with "placeholder one" and "placeholder two"',
            (string)__('Message with "%1" and "%2"', 'placeholder one', 'placeholder two')
        );
    }
}
