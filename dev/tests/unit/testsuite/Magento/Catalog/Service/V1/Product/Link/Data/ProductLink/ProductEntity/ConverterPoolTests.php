<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Catalog\Service\V1\Product\Link\Data\ProductLink\ProductEntity;

class ConverterPoolTests extends \PHPUnit_Framework_TestCase
{
    /**
     * @var \Magento\Catalog\Service\V1\Product\Link\Data\ProductLink\ProductEntity\ConverterPool
     */
    protected $model;

    protected function setUp()
    {
        $converters = array(
            'simple' => 'Simple Converter',
            'complex' => 'Complex Converter',
            'default' => 'Default Converter',
        );

        $this->model = new \Magento\Catalog\Service\V1\Product\Link\Data\ProductLink\ProductEntity\ConverterPool(
            $converters
        );
    }

    public function testGetConverterExisting()
    {
        $this->assertEquals('Simple Converter', $this->model->getConverter('simple'));
    }

    public function testGetConverterAbsent()
    {
        $this->assertEquals('Default Converter', $this->model->getConverter('absent'));
    }
}
