<?php
/**
 * Integration test for \Magento\GoogleAdwords\Model\Validator\Factory
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\GoogleAdwords\Model\Validator;

class FactoryTest extends \PHPUnit_Framework_TestCase
{
    /**
     * Test creation of conversion id validator
     *
     * @magentoAppIsolation enabled
     */
    public function testGetConversionIdValidator()
    {
        $conversionId = '123';

        $objectManager = \Magento\TestFramework\Helper\Bootstrap::getObjectManager();
        $factory = $objectManager->get('Magento\GoogleAdwords\Model\Validator\Factory');

        $validator = $factory->createConversionIdValidator($conversionId);
        $this->assertNotNull($validator, "Conversion ID Validator");
    }
}
