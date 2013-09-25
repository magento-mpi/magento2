<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Core\Helper;

class AbstractTest extends \PHPUnit_Framework_TestCase
{
    public function testRemoveTags()
    {
        $input = '<div>10</div> < <a>11</a> > <span>10</span>';
        /** @var \Magento\Core\Helper\AbstractHelper $helper */
        $helper = $this->getMockForAbstractClass('Magento\Core\Helper\AbstractHelper', array(), '', false);
        $actual = $helper->removeTags($input);
        $expected = '10 < 11 > 10';
        $this->assertSame($expected, $actual);
    }
}
