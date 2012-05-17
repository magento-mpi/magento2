<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Mage_Adminhtml
 * @subpackage  unit_tests
 * @copyright   {copyright}
 * @license     {license_link}
 */

class Mage_Adminhtml_Block_UrlrewriteTest extends PHPUnit_Framework_TestCase
{
    /**
     * @param array $modes
     * @param string $expectedUrl
     * @dataProvider getCreateUrlData
     */
    public function testGetCreateUrl(array $modes, $expectedUrl)
    {
        /** @var $selectorBlock Mage_Adminhtml_Block_Urlrewrite_Selector */
        $selectorBlock = $modes
            ? $this->getMock('Mage_Adminhtml_Block_Urlrewrite_Selector', array('getModes'), array(), '', false)
            : false;
        if ($selectorBlock) {
            $selectorBlock->expects($this->once())->method('getModes')->with()->will($this->returnValue($modes));
        }

        $testedBlock = $this->getMock('Mage_Adminhtml_Block_Urlrewrite', array('getUrl'), array(), '', false);
        $testedBlock->setSelectorBlock($selectorBlock);
        $testedBlock->expects($this->once())
            ->method('getUrl')
            ->with('*/*/edit')
            ->will($this->returnValue('http://localhost/admin/urlrewrite/edit/'));

        $this->assertEquals($expectedUrl, $testedBlock->getCreateUrl());
    }

    /**
     * @static
     * @return array
     */
    public static function getCreateUrlData()
    {
        return array(
            array(
                array(),
                'http://localhost/admin/urlrewrite/edit/',
            ),
            array(
                array('category' => 'For category', 'product' => 'For product', 'id' => 'Custom'),
                'http://localhost/admin/urlrewrite/edit/category',
            ),
            array(
                array('product' => 'For product', 'category' => 'For category', 'id' => 'Custom'),
                'http://localhost/admin/urlrewrite/edit/product',
            ),
            array(
                array('id' => 'Custom', 'product' => 'For product', 'category' => 'For category'),
                'http://localhost/admin/urlrewrite/edit/id',
            ),
        );
    }
}
