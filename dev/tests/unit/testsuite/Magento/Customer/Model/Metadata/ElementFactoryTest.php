<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Customer\Model\Metadata;

class ElementFactoryTest extends \PHPUnit_Framework_TestCase
{
    /** @var \Magento\ObjectManager | \PHPUnit_Framework_MockObject_MockObject */
    private $_objectManager;

    /** @var \Magento\Customer\Service\V1\Data\Eav\AttributeMetadata | \PHPUnit_Framework_MockObject_MockObject */
    private $_attributeMetadata;

    /** @var string */
    private $_entityTypeCode = 'customer_address';

    /** @var ElementFactory */
    private $_elementFactory;

    public function setUp()
    {
        $this->_objectManager = $this->getMock('Magento\ObjectManager', array(), array(), '', false);
        $this->_attributeMetadata = $this->getMock(
            'Magento\Customer\Service\V1\Data\Eav\AttributeMetadata',
            array(),
            array(),
            '',
            false
        );
        $this->_elementFactory = new ElementFactory($this->_objectManager, new \Magento\Stdlib\String());
    }

    /** TODO fix when Validation is implemented MAGETWO-17341 */
    public function testAttributePostcodeDataModelClass()
    {
        $this->_attributeMetadata->expects(
            $this->once()
        )->method(
            'getDataModel'
        )->will(
            $this->returnValue('Magento\Customer\Model\Attribute\Data\Postcode')
        );

        $dataModel = $this->getMock('Magento\Customer\Model\Metadata\Form\Text', array(), array(), '', false);
        $this->_objectManager->expects($this->once())->method('create')->will($this->returnValue($dataModel));

        $actual = $this->_elementFactory->create($this->_attributeMetadata, '95131', $this->_entityTypeCode);
        $this->assertSame($dataModel, $actual);
    }

    public function testAttributeEmptyDataModelClass()
    {
        $this->_attributeMetadata->expects($this->once())->method('getDataModel')->will($this->returnValue(''));
        $this->_attributeMetadata->expects(
            $this->once()
        )->method(
            'getFrontendInput'
        )->will(
            $this->returnValue('text')
        );

        $dataModel = $this->getMock('Magento\Customer\Model\Metadata\Form\Text', array(), array(), '', false);
        $params = array(
            'entityTypeCode' => $this->_entityTypeCode,
            'value' => 'Some Text',
            'isAjax' => false,
            'attribute' => $this->_attributeMetadata
        );
        $this->_objectManager->expects(
            $this->once()
        )->method(
            'create'
        )->with(
            'Magento\Customer\Model\Metadata\Form\Text',
            $params
        )->will(
            $this->returnValue($dataModel)
        );

        $actual = $this->_elementFactory->create($this->_attributeMetadata, 'Some Text', $this->_entityTypeCode);
        $this->assertSame($dataModel, $actual);
    }
}
