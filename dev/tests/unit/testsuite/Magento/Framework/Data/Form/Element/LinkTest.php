<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Data
 * @subpackage  integration_tests
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Tests for \Magento\Framework\Data\Form\Element\Link
 */
namespace Magento\Framework\Data\Form\Element;

class LinkTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    protected $_objectManagerMock;

    /**
     * @var \Magento\Framework\Data\Form\Element\Link
     */
    protected $_link;

    protected function setUp()
    {
        $factoryMock = $this->getMock('\Magento\Framework\Data\Form\Element\Factory', array(), array(), '', false);
        $collectionFactoryMock = $this->getMock(
            '\Magento\Framework\Data\Form\Element\CollectionFactory',
            array(),
            array(),
            '',
            false
        );
        $escaperMock = $this->getMock('\Magento\Framework\Escaper', array(), array(), '', false);
        $this->_link = new \Magento\Framework\Data\Form\Element\Link(
            $factoryMock,
            $collectionFactoryMock,
            $escaperMock
        );
        $formMock = new \Magento\Framework\Object();
        $formMock->getHtmlIdPrefix('id_prefix');
        $formMock->getHtmlIdPrefix('id_suffix');
        $this->_link->setForm($formMock);
    }

    /**
     * @covers \Magento\Framework\Data\Form\Element\Link::__construct
     */
    public function testConstruct()
    {
        $this->assertEquals('link', $this->_link->getType());
    }

    /**
     * @covers \Magento\Framework\Data\Form\Element\Link::getElementHtml
     */
    public function testGetElementHtml()
    {
        $this->_link->setBeforeElementHtml('link_before');
        $this->_link->setAfterElementHtml('link_after');
        $this->_link->setId('link_id');
        $this->_link->setData('ui_id', 'ui_id');
        $this->_link->setValue('Link Text');
        $html = $this->_link->getElementHtml();
        $this->assertEquals(
            "link_before<a id=\"link_id\"  data-ui-id=\"form-element-\">Link Text</a>\nlink_after",
            $html
        );
    }

    /**
     * @covers \Magento\Framework\Data\Form\Element\Link::getHtmlAttributes
     */
    public function testGetHtmlAttributes()
    {
        $this->assertEmpty(
            array_diff(
                array(
                    'charset',
                    'coords',
                    'href',
                    'hreflang',
                    'rel',
                    'rev',
                    'name',
                    'shape',
                    'target',
                    'accesskey',
                    'class',
                    'dir',
                    'lang',
                    'style',
                    'tabindex',
                    'title',
                    'xml:lang',
                    'onblur',
                    'onclick',
                    'ondblclick',
                    'onfocus',
                    'onmousedown',
                    'onmousemove',
                    'onmouseout',
                    'onmouseover',
                    'onmouseup',
                    'onkeydown',
                    'onkeypress',
                    'onkeyup'
                ),
                $this->_link->getHtmlAttributes()
            )
        );
    }
}
