<?php
/**
 * {license_notice}
 *
 * @copyright {copyright}
 * @license {license_link}
 */
namespace Magento\Catalog\Service\V1\Product\Attribute;

use Magento\Catalog\Service\V1\Data\Eav\AttributeMetadata;
use Magento\Catalog\Service\V1\Data\Eav\AttributeMetadataBuilder;

class WriteServiceTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var \Magento\Catalog\Service\V1\Data\Eav\AttributeMetadataBuilder
     */
    protected $attributeMetadataBuilder;

    /**
     * @var \Magento\Catalog\Model\Resource\Eav\Attribute | \PHPUnit_Framework_MockObject_MockObject
     */
    protected $attributeMock;

    /**
     * @var \Magento\Eav\Model\Adminhtml\System\Config\Source\Inputtype\Validator
     */
    protected $inputValidator;

    /**
     * @var \Magento\Catalog\Service\V1\Product\Attribute\WriteService
     */
    protected $attributeWriteService;

    public function setUp()
    {
        $objectManager = new \Magento\TestFramework\Helper\ObjectManager($this);
        $this->attributeMetadataBuilder = $objectManager
            ->getObject('Magento\Catalog\Service\V1\Data\Eav\AttributeMetadataBuilder');

        $this->attributeMock = $this->getMock('\Magento\Catalog\Model\Resource\Eav\Attribute', [], [], '', false);
        $attributeFactory =
            $this->getMock('\Magento\Catalog\Model\Resource\Eav\AttributeFactory', ['create'], [], '', false);
        $attributeFactory
            ->expects($this->any())
            ->method('create')
            ->will($this->returnValue($this->attributeMock));

        $this->inputValidator =
            $this->getMock('\Magento\Eav\Model\Adminhtml\System\Config\Source\Inputtype\Validator', [], [], '', false);
        $inputValidatorFactory =
            $this->getMock(
                '\Magento\Eav\Model\Adminhtml\System\Config\Source\Inputtype\ValidatorFactory',
                ['create'], [], '', false
            );
        $inputValidatorFactory
            ->expects($this->any())
            ->method('create')
            ->will($this->returnValue($this->inputValidator));

        $this->attributeWriteService = $objectManager->getObject(
            '\Magento\Catalog\Service\V1\Product\Attribute\WriteService',
            [
                'attributeFactory' => $attributeFactory,
                'inputtypeValidatorFactory' => $inputValidatorFactory
            ]
        );
    }

    public function testUpdate()
    {
        $attributeObject = $this->getMockBuilder('\Magento\Catalog\Service\V1\Data\Eav\AttributeMetadata')
            ->disableOriginalConstructor()
            ->getMock();

        $attributeCode = 'color';
        $this->attributeMock
            ->expects($this->at(0))
            ->method('loadByCode')
            ->with(
                $this->equalTo(\Magento\Catalog\Model\Product::ENTITY),
                $this->equalTo($attributeCode)
            );

        $attributeObject
            ->expects($this->once())
            ->method('__toArray')
            ->will($this->returnValue(
               [
                   AttributeMetadata::FILTERABLE => 2,
                   AttributeMetadata::FRONTEND_LABEL => [
                       [
                           'store_id' => 1,
                           'label'    => 'Label for store 1'
                       ],
                   ],
                   AttributeMetadata::APPLY_TO => [
                       'simple',
                       'virtual'
                   ]
               ]
            ));


        // check that methods will be called
        $this->attributeMock->expects($this->at(1))->method('getId')->will($this->returnValue(1));
        $this->attributeMock->expects($this->at(2))->method('getAttributeId')->will($this->returnValue(1));
        // cover "getIsUserDefined" method - uses __call because "getIsUserDefined" magic method
        $this->attributeMock->expects($this->at(3))->method('__call')->will($this->returnValue(true));
        // cover "getFrontendInput" method - uses __call because "getFrontendInput" magic method
        $this->attributeMock->expects($this->at(4))->method('__call')->will($this->returnValue('select'));
        // cover "getFrontendLabel" method - uses __call because "getFrontendLabel" magic method
        $this->attributeMock->expects($this->at(5))->method('__call')
            ->will($this->returnValue('Label'));
        // cover "getIsUserDefined" method - uses __call because "getIsUserDefined" magic method
        // return false to check unset of element with "apply_to" key
        $this->attributeMock->expects($this->at(6))->method('__call')->will($this->returnValue(false));
        // absent of "apply_to" key also checks here - because of false in previous call
        $this->attributeMock->expects($this->at(7))->method('addData')
            ->with(
                [
                    'filterable'     => 2,
                    'frontend_label' => [0 => 'Label', 1 => 'Label for store 1'],
                    'attribute_id'   => 1,
                    'user_defined'   => true,
                    'frontend_input' => 'select'
                ]
            );

        $this->attributeMock->expects($this->at(8))->method('save');
        $this->attributeMock->expects($this->at(9))->method('getAttributeCode')
            ->will($this->returnValue($attributeCode));

        // run process
        $this->attributeWriteService->update($attributeCode, $attributeObject);
    }
}
