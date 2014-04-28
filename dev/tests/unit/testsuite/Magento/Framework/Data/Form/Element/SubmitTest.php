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
 * Tests for \Magento\Framework\Data\Form\Element\Submit
 */
namespace Magento\Framework\Data\Form\Element;

class SubmitTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    protected $_objectManagerMock;

    /**
     * @var \Magento\Framework\Data\Form\Element\Submit
     */
    protected $_model;

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
        $this->_model = new \Magento\Framework\Data\Form\Element\Submit(
            $factoryMock,
            $collectionFactoryMock,
            $escaperMock
        );
        $formMock = new \Magento\Framework\Object();
        $formMock->getHtmlIdPrefix('id_prefix');
        $formMock->getHtmlIdPrefix('id_suffix');
        $this->_model->setForm($formMock);
    }

    /**
     * @covers \Magento\Framework\Data\Form\Element\Submit::__construct
     */
    public function testConstruct()
    {
        $this->assertEquals('submit', $this->_model->getType());
        $this->assertEquals('submit', $this->_model->getExtType());
    }

    /**
     * @covers \Magento\Framework\Data\Form\Element\Submit::getHtml
     */
    public function testGetHtml()
    {
        $html = $this->_model->getHtml();
        $this->assertContains('type="submit"', $html);
        $this->assertTrue(preg_match('/class=\".*submit.*\"/i', $html) > 0);
    }
}
