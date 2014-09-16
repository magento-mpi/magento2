<?php
/**
 * {license_notice}
 *
 * @copyright  {copyright}
 * @license    {license_link}
 */

namespace Magento\Test\Tools\Composer\Helper;

class ReplaceFilterTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @param string $name
     * @param bool $expected
     * @dataProvider isMagentoComponentDataProvider
     */
    public function testIsMagentoComponent($name, $expected)
    {
        $this->assertEquals($expected, \Magento\Tools\Composer\Helper\ReplaceFilter::isMagentoComponent($name));
    }

    /**
     * @return array
     */
    public function isMagentoComponentDataProvider()
    {
        return [
            ['magento/module', true],
            ['magento/module-foo', true],
            ['magento/theme', true],
            ['magento/theme-frontend', true],
            ['magento/theme-frontend-foo', true],
            ['magento/theme-adminhtml', true],
            ['magento/theme-adminhtml-bar', true],
            ['magento/language', true],
            ['magento/language-foo', true],
            ['magento/framework', true],
            ['magento/framework-bar', true],
            ['magento/anything-else', false],
            ['vendor/module', false],
            ['vendor/module-foo', false],
        ];
    }
}
