<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Customer\Model\Metadata;

class ValidatorTest extends \PHPUnit_Framework_TestCase
{
    /** @var Validator */
    protected $validator;

    /** @var string */
    protected $entityType;

    /** @var \Magento\Customer\Model\Metadata\ElementFactory | \PHPUnit_Framework_MockObject_MockObject */
    protected $attrDataFactoryMock;

    public function setUp()
    {
        $this->attrDataFactoryMock = $this->getMockBuilder('\Magento\Customer\Model\Metadata\ElementFactory')
            ->disableOriginalConstructor()
            ->getMock();

        $this->validator = new Validator($this->attrDataFactoryMock);
    }

    /**
     * @param null|\Magento\Customer\Model\Metadata\Form\AbstractData dataModel
     * @param \Magento\Customer\Model\Attribute                       $attribute
     * @param bool                                                    $isValid
     * @dataProvider validateDataDataProvider
     */
    public function testValidateData($dataModel, $attribute, $isValid)
    {
        $this->attrDataFactoryMock
            ->expects($this->any())
            ->method('create')
            ->withAnyParameters()
            ->will($this->returnValue($dataModel));
        $this->assertEquals($isValid, $this->validator->validateData([], [$attribute], 'ENTITY_TYPE'));
    }

    /**
     * @param null|\Magento\Customer\Model\Metadata\Form\AbstractData dataModel
     * @param \Magento\Customer\Model\Attribute                       $attribute
     * @param bool                                                    $isValid
     * @dataProvider validateDataDataProvider
     */
    public function testIsValid($dataModel, $attribute, $isValid)
    {
        $this->attrDataFactoryMock
            ->expects($this->any())
            ->method('create')
            ->withAnyParameters()
            ->will($this->returnValue($dataModel));
        $this->validator->setAttributes([$attribute]);
        $this->validator->setEntityType('ENTITY_TYPE');
        $this->validator->setData(['something']);
        $this->assertEquals($isValid, $this->validator->isValid('entity'));
        $this->validator->setData([]);
        $this->assertEquals($isValid, $this->validator->isValid(new \Magento\Object([])));
    }

    public function validateDataDataProvider()
    {
        $testCases = [];

        $attribute = $this->getMockBuilder('\Magento\Customer\Service\V1\Dto\Eav\AttributeMetadata')
            ->disableOriginalConstructor()
            ->getMock();
        $testCases['No data model or frontend'] = [null, $attribute, true];

        $attribute = $this->getMockBuilder('\Magento\Customer\Service\V1\Dto\Eav\AttributeMetadata')
            ->disableOriginalConstructor()
            ->setMethods(['__wakeup', 'getAttributeCode', 'getDataModel'])
            ->getMock();
        $attribute->expects($this->any())
            ->method('getAttributeCode')
            ->will($this->returnValue('ATTR_CODE'));
        $attribute->expects($this->any())
            ->method('getDataModel')
            ->will($this->returnValue('string'));
        $dataModel = $this->getMockBuilder('\Magento\Customer\Model\Metadata\Form\Text')
            ->disableOriginalConstructor()
            ->getMock();
        $dataModel->expects($this->any())
            ->method('validateValue')
            ->will($this->returnValue(true));
        $testCases['Successful Validation'] = [$dataModel, $attribute, true];

        $dataModel = $this->getMockBuilder('\Magento\Customer\Model\Metadata\Form\Text')
            ->disableOriginalConstructor()
            ->getMock();
        $dataModel->expects($this->any())
            ->method('validateValue')
            ->will($this->returnValue(false));
        $testCases['Failed Validation'] = [$dataModel, $attribute, false];

        return $testCases;
    }
}
 