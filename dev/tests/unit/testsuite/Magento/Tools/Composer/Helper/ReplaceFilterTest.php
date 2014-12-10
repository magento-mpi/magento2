<?php
/**
 * @copyright Copyright (c) 2014 X.commerce, Inc. (http://www.magentocommerce.com)
 */

namespace Magento\Tools\Composer\Helper;

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

    public function testMoveMagentoComponentsToRequire()
    {
        $replaceMap = [
            'magento/framework' => 'self.version',
            'magento/module-store' => 'self.version',
            'magento/module-fedex' => 'self.version',
            'symfony/yaml' => 'self.version',
        ];
        $package = $this->getMockBuilder('Magento\Tools\Composer\Package\Package')
            ->disableOriginalConstructor()->getMock();
        $package->expects($this->any())
            ->method('get')
            ->willReturnMap([
                    ['replace', null, $replaceMap],
                    ['suggest', null, ['magento/module-fedex' => 'comment']],
                    ['replace->magento/framework', null, 'self.version'],
                    ['replace->magento/module-store', null, 'self.version'],
                    ['replace->magento/module-fedex', null, 'self.version'],
                    ['suggest->magento/module-fedex', null, '0.1.0-alpha1337'],
                ]);
        $package->expects($this->exactly(2))
            ->method('unsetProperty')
            ->withConsecutive(
                [$this->equalTo('replace->magento/framework')],
                [$this->equalTo('replace->magento/module-store')]
            );
        $package->expects($this->exactly(2))
            ->method('set')
            ->withConsecutive(
                [$this->equalTo('require->magento/framework'), $this->anything()],
                [$this->equalTo('require->magento/module-store'), $this->anything()]
            );

        $replaceFilter = new \Magento\Tools\Composer\Helper\ReplaceFilter(null);

        $replaceFilter->moveMagentoComponentsToRequire($package, false);
    }
}
