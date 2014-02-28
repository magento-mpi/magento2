<?php
/**
 * {license_notice}
 *
 * @subpackage  unit_tests
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\View;

class PathTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @param string $area
     * @param string $theme
     * @param string $locale
     * @param string $module
     * @param string $expected
     * @dataProvider getFullyQualifiedPathDataProvider
     */
    public function testGetFullyQualifiedDirPath($area, $theme, $locale, $module, $expected)
    {
        $path = new Path;
        $this->assertEquals($expected, $path->getFullyQualifiedPath($area, $theme, $locale, $module));
    }

    /**
     * @return array
     */
    public function getFullyQualifiedPathDataProvider()
    {
        return array(
            array('2', '3', '4', '5', '2/3/4/5'),
            array('2', '3', '4', '', '2/3/4'),
            array('2', '3', '4', null, '2/3/4'),
        );
    }
}
