<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\UrlRewrite\Model\UrlRewrite;

class TypeProviderTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @covers \Magento\UrlRewrite\Model\UrlRewrite\TypeProvider::getAllOptions
     */
    public function testGetAllOptions()
    {
        $model = new \Magento\UrlRewrite\Model\UrlRewrite\TypeProvider();
        $options = $model->getAllOptions();
        $this->assertInternalType('array', $options);
        $expectedOptions = array(1 => 'System', 0 => 'Custom');
        $this->assertEquals($expectedOptions, $options);
    }
}
