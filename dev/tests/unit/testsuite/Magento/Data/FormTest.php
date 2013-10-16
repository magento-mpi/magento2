<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Data;

/**
 * Tests for \Magento\Data\Form\Factory
 */
class FormTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    protected $_factoryElement;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    protected $_factoryCollection;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    protected $_sessionMock;

    /**
     * @var \Magento\Data\Form
     */
    protected $_form;

    protected function setUp()
    {
        $this->_factoryElement = $this->getMock('Magento\Data\Form\Element\Factory',
            array('create'), array(), '', false);
        $this->_factoryCollection = $this->getMock('Magento\Data\Form\Element\CollectionFactory',
            array('create'), array(), '', false);
        $this->_factoryCollection->expects($this->any())->method('create')->will($this->returnValue(array()));
        $this->_sessionMock = $this->getMock('Magento\Core\Model\Session\AbstractSession', array(), array(), '', false);

        $this->_form = new Form($this->_factoryElement, $this->_factoryCollection);
    }

    public function testSetSession()
    {
        $this->assertSame($this->_form, $this->_form->setSession($this->_sessionMock));
    }

    /**
     * @expectedException \Exception
     */
    public function testSetSessionError()
    {
        $this->_form->setSession(new \stdClass());
    }

    /**
     * @expectedException \Magento\Exception
     */
    public function testToHtmlThrowException()
    {
        $this->_form->setUseContainer(true);
        $this->_form->setMethod('post');
        $this->_form->toHtml();
    }

    public function testFormKeyExist()
    {
        $this->_form->setUseContainer(true);
        $this->_form->setMethod('post');
        $this->_form->setSession($this->_sessionMock);
        $this->assertContains('form_key', $this->_form->toHtml());
    }
}
