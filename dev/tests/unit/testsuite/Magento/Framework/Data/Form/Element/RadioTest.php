<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Tests for \Magento\Framework\Data\Form\Element\Radio
 */
namespace Magento\Framework\Data\Form\Element;

class RadioTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    protected $_objectManagerMock;

    /**
     * @var \Magento\Framework\Data\Form\Element\Radio
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
        $this->_model = new \Magento\Framework\Data\Form\Element\Radio(
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
     * @covers \Magento\Framework\Data\Form\Element\Radio::__construct
     */
    public function testConstruct()
    {
        $this->assertEquals('radio', $this->_model->getType());
        $this->assertEquals('radio', $this->_model->getExtType());
    }
}
