<?php
/**
 * @copyright Copyright (c) 2014 X.commerce, Inc. (http://www.magentocommerce.com)
 */
namespace Magento\Framework\Filter;

class RemoveTagsTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @covers \Magento\Framework\Filter\RemoveTags::filter
     * @covers \Magento\Framework\Filter\RemoveTags::_convertEntities
     */
    public function testRemoveTags()
    {
        $input = '<div>10</div> < <a>11</a> > <span>10</span>';
        $removeTags = new \Magento\Framework\Filter\RemoveTags();
        $actual = $removeTags->filter($input);
        $expected = '10 < 11 > 10';
        $this->assertSame($expected, $actual);
    }
}
