<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

class Magento_Core_Helper_AbstractTest extends PHPUnit_Framework_TestCase
{
    public function testRemoveTags()
    {
        $input = '<div>10</div> < <a>11</a> > <span>10</span>';
        /** @var Magento_Core_Helper_Abstract $helper */
        $helper = $this->getMockForAbstractClass('Magento_Core_Helper_Abstract', array(), '', false);
        $actual = $helper->removeTags($input);
        $expected = '10 < 11 > 10';
        $this->assertSame($expected, $actual);
    }
}
