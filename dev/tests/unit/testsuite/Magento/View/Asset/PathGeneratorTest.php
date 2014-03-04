<?php
/**
 * {license_notice}
 *
 * @subpackage  unit_tests
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\View\Asset;

class PathGeneratorTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @param string $area
     * @param string $theme
     * @param string $locale
     * @param string $module
     * @param string $expected
     * @dataProvider getPathDataProvider
     */
    public function testGetPath($area, $theme, $locale, $module, $expected)
    {
        $path = new PathGenerator;
        $this->assertEquals($expected, $path->getPath($area, $theme, $locale, $module));
    }

    /**
     * @return array
     */
    public function getPathDataProvider()
    {
        return array(
            array('2', '3', '4', '5', '2/3/4/5'),
            array('2', '3', '4', '', '2/3/4'),
            array('2', '3', '4', null, '2/3/4'),
        );
    }
}
