<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Filter;

class RemoveTagsTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @covers \Magento\Filter\RemoveTags::filter
     * @covers \Magento\Filter\RemoveTags::_convertEntities
     */
    public function testRemoveTags()
    {
        $input = '<div>10</div> < <a>11</a> > <span>10</span>';
        $removeTags = new \Magento\Filter\RemoveTags();
        $actual = $removeTags->filter($input);
        $expected = '10 < 11 > 10';
        $this->assertSame($expected, $actual);
    }
}
