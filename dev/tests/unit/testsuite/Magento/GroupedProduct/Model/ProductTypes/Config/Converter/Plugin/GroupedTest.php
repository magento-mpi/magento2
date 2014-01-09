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
     * @param $config array
     * @param $result array
     * @dataProvider configProvider
     */
    public function testAfterConvert($config, $result)
    {
        $model = new \Magento\GroupedProduct\Model\ProductTypes\Config\Converter\Plugin\Grouped();
        $this->assertEquals($result, $model->afterConvert($config));
    }

    public function configProvider()
    {
        $emptyConfig = array(1, 2, 3);
        $config = array('grouped' => array(1));
        $result = array('grouped' => array(1, 'is_product_set' => true));

        return array(
            'empty config' => array($emptyConfig, $emptyConfig),
            'with grouped' => array($config, $result),
        );
    }
}
