<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Core\Helper;

class StripTagsTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @covers \Magento\Filter\StripTags::_construct
     * @covers \Magento\Filter\StripTags::filter
     */
    public function testStripTags()
    {
        $stripTags = new \Magento\Filter\StripTags(new \Magento\Escaper());
        $this->assertEquals('three', $stripTags->filter('<two>three</two>'));
    }
}
