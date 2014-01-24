<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Catalog\Model\Product;

class CartConfigurationTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @param string $productType
     * @param array $config
     * @param boolean $expected
     * @dataProvider isProductConfiguredDataProvider
     */
    public function testIsProductConfigured($productType, $config, $expected)
    {
        $cartConfiguration = new \Magento\Catalog\Model\Product\CartConfiguration();
        $productMock = $this->getMock('Magento\Catalog\Model\Product', array(), array(), '', false);
        $productMock->expects($this->once())->method('getTypeId')->will($this->returnValue($productType));
        $this->assertEquals($expected, $cartConfiguration->isProductConfigured($productMock, $config));
    }

    public function isProductConfiguredDataProvider()
    {
        return array(
            'simple' => array('simple', array(), false),
            'virtual' => array('virtual', array('options' => true), true),
            'configurable' => array('configurable',array('super_attribute' => false), true),
            'bundle' => array('bundle', array('bundle_option' => 'option1'), true),
            'giftcard' => array('giftcard', array(), false),
            'downloadable' => array('downloadable', array('links' => 'option1'), true),
            'some_option_type' => array('some_option_type', array(), false)
        );
    }
}

