<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Framework\Data;

/**
 * Tests for \Magento\Framework\Data\FormFactory
 * @SuppressWarnings(PHPMD.LongVariable)
 */
class FormTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    protected $_factoryElementMock;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    protected $_factoryCollectionMock;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    protected $_formKeyMock;

    /**
     * @var \Magento\Framework\Data\Form
     */
    protected $_form;

    protected function setUp()
    {
        $this->_factoryElementMock = $this->getMock(
            'Magento\Framework\Data\Form\Element\Factory',
            array('create'),
            array(),
            '',
            false
        );
        $this->_factoryCollectionMock = $this->getMock(
            'Magento\Framework\Data\Form\Element\CollectionFactory',
            array('create'),
            array(),
            '',
            false
        );
        $this->_factoryCollectionMock->expects($this->any())->method('create')->will($this->returnValue(array()));
        $this->_formKeyMock = $this->getMock(
            'Magento\Framework\Data\Form\FormKey',
            array('getFormKey'),
            array(),
            '',
            false
        );

        $this->_form = new Form($this->_factoryElementMock, $this->_factoryCollectionMock, $this->_formKeyMock);
    }

    public function testFormKeyUsing()
    {
        $formKey = 'form-key';
        $this->_formKeyMock->expects($this->once())->method('getFormKey')->will($this->returnValue($formKey));

        $this->_form->setUseContainer(true);
        $this->_form->setMethod('post');
        $this->assertContains($formKey, $this->_form->toHtml());
    }
}
