<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Core\Helper;

class RemoveTagsTest extends \PHPUnit_Framework_TestCase
{
    public function testRemoveTags()
    {
        $input = '<div>10</div> < <a>11</a> > <span>10</span>';
        $removeTags = new \Magento\Filter\RemoveTags();
        $actual = $removeTags->filter($input);
        $expected = '10 < 11 > 10';
        $this->assertSame($expected, $actual);
    }
}
