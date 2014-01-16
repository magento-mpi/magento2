<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\GroupedProduct\Model\ProductTypes\Config\Converter\Plugin;

class GroupedTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @param array $config
     * @param array $result
     * @dataProvider afterConvertDataProvider
     */
    public function testAfterConvert($config, $result)
    {
        $model = new \Magento\GroupedProduct\Model\ProductTypes\Config\Converter\Plugin\Grouped();
        $this->assertEquals($result, $model->afterConvert($config));
    }

    /**
     * @return array
     */
    public function afterConvertDataProvider()
    {
        $index = \Magento\GroupedProduct\Model\Product\Type\Grouped::TYPE_CODE;
        $emptyConfig = array(1, 2, 3);
        $config = array($index => array(1));
        $result = array($index => array(1, 'is_product_set' => true));

        return array(
            'empty config' => array($emptyConfig, $emptyConfig),
            'with grouped' => array($config, $result),
        );
    }
}
