<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Eav
 * @subpackage  unit_tests
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Test for Magento_Eav_Model_Validator_Attribute_Data
 */
class Magento_Eav_Model_Validator_Attribute_DataTest extends PHPUnit_Framework_TestCase
{
    /**
     * Testing  Magento_Eav_Model_Validator_Attribute_Data::isValid
     *
     * @dataProvider isValidDataProvider
     *
     * @param array $attributeData
     * @param array|bool $result
     * @param bool $expected
     * @param array $messages
     * @param array $data
     */
    public function testIsValid($attributeData, $result, $expected, $messages, $data = array('attribute' => 'new_test'))
    {
        $entity = $this->_getEntityMock();
        $attribute = $this->_getAttributeMock($attributeData);

        $validator = new Magento_Eav_Model_Validator_Attribute_Data;
        $validator->setAttributes(array($attribute))
            ->setData($data);
        if ($attribute->getDataModel() || $attribute->getFrontendInput()) {
            $dataModel = $this->_getDataModelMock($result);
            $factory = $this->_getFactoryMock($dataModel);
            $validator->setAttributeDataModelFactory($factory);
        }
        $this->assertEquals($expected, $validator->isValid($entity));
        $this->assertEquals($messages, $validator->getMessages());
    }

    /**
     * Data provider for testIsValid
     *
     * @return array
     */
    public function isValidDataProvider()
    {
        return array(
            'is_valid' => array(
                'attributeData' => array(
                    'attribute_code' => 'attribute',
                    'data_model' => $this->_getDataModelMock(null),
                    'frontend_input' => 'text'
                ),
                'attributeReturns' => true,
                'isValid' => true,
                'messages' => array()
            ),
            'is_invalid' => array(
                'attributeData' => array(
                    'attribute_code' => 'attribute',
                    'data_model' => $this->_getDataModelMock(null),
                    'frontend_input' => 'text'
                ),
                'attributeReturns' => array('Error'),
                'isValid' => false,
                'messages' => array('attribute' => array('Error'))
            ),
            'no_data_models' => array(
                'attributeData' => array(
                    'attribute_code' => 'attribute',
                    'frontend_input' => 'text'
                ),
                'attributeReturns' => array('Error'),
                'isValid' => false,
                'messages' => array('attribute' => array('Error'))
            ),
            'no_data_models_no_frontend_input' => array(
                'attributeData' => array(
                    'attribute_code' => 'attribute',
                ),
                'attributeReturns' => array('Error'),
                'isValid' => true,
                'messages' => array()
            ),
            'no_data_for attribute' => array(
                'attributeData' => array(
                    'attribute_code' => 'attribute',
                    'data_model' => $this->_getDataModelMock(null),
                    'frontend_input' => 'text'
                ),
                'attributeReturns' => true,
                'isValid' => true,
                'messages' => array(),
                'setData' => array('attribute2' => 'new_test')
            ),
            'is_valid_data_from_entity' => array(
                'attributeData' => array(
                    'attribute_code' => 'attribute',
                    'data_model' => $this->_getDataModelMock(null),
                    'frontend_input' => 'text'
                ),
                'attributeReturns' => true,
                'isValid' => true,
                'messages' => array(),
                'setData' => array()
            ),
        );
    }

    /**
     * Testing Magento_Eav_Model_Validator_Attribute_Data::isValid
     *
     * In this test entity attributes are got from attribute collection.
     */
    public function testIsValidAttributesFromCollection()
    {
        /** @var Magento_Eav_Model_Entity_Abstract $resource */
        $resource = $this->getMockForAbstractClass('Magento_Eav_Model_Entity_Abstract');
        $attribute = $this->_getAttributeMock(array(
            'attribute_code' => 'attribute',
            'data_model' => $this->_getDataModelMock(null),
            'frontend_input' => 'text'
        ));
        $collection = $this->getMockBuilder('Magento_Object')
            ->setMethods(array('getItems'))
            ->getMock();
        $collection->expects($this->once())->method('getItems')->will($this->returnValue(array($attribute)));
        $entityType = $this->getMockBuilder('Magento_Object')
            ->setMethods(array('getAttributeCollection'))
            ->getMock();
        $entityType->expects($this->once())->method('getAttributeCollection')->will($this->returnValue($collection));
        $entity = $this->_getEntityMock();
        $entity->expects($this->once())->method('getResource')->will($this->returnValue($resource));
        $entity->expects($this->once())->method('getEntityType')->will($this->returnValue($entityType));
        $dataModel = $this->_getDataModelMock(true);
        $factory = $this->_getFactoryMock($dataModel);

        $validator = new Magento_Eav_Model_Validator_Attribute_Data;
        $validator->setData(array('attribute' => 'new_test_data'))
            ->setAttributeDataModelFactory($factory);
        $this->assertTrue($validator->isValid($entity));
    }

    /**
     * @dataProvider whiteBlackListProvider
     * @param callable $callback
     */
    public function testIsValidBlackListWhiteListChecks($callback)
    {
        $attribute = $this->_getAttributeMock(array(
            'attribute_code' => 'attribute',
            'data_model' => $this->_getDataModelMock(null),
            'frontend_input' => 'text'
        ));
        $secondAttribute = $this->_getAttributeMock(array(
            'attribute_code' => 'attribute2',
            'data_model' => $this->_getDataModelMock(null),
            'frontend_input' => 'text'
        ));
        $data = array(
            'attribute' => 'new_test_data',
            'attribute2' => 'some data'
        );
        $entity = $this->_getEntityMock();
        $dataModel = $this->_getDataModelMock(true, $data['attribute']);
        $factory = $this->_getFactoryMock($dataModel);

        $validator = new Magento_Eav_Model_Validator_Attribute_Data;
        $validator->setAttributeDataModelFactory($factory)
            ->setAttributes(array($attribute, $secondAttribute))
            ->setData($data);
        $callback($validator);
        $this->assertTrue($validator->isValid($entity));
    }

    /**
     * @return array
     */
    public function whiteBlackListProvider()
    {
        $whiteCallback = function($validator) {
            $validator->setAttributesWhiteList(array('attribute'));
        };

        $blackCallback = function($validator) {
            $validator->setAttributesBlackList(array('attribute2'));
        };
        return array(
            'white_list' => array($whiteCallback),
            'black_list' => array($blackCallback)
        );
    }

    public function testSetAttributesWhiteList()
    {
        $attributes = array('attr1', 'attr2', 'attr3');
        $validator = new Magento_Eav_Model_Validator_Attribute_Data;
        $result = $validator->setAttributesWhiteList($attributes);
        $this->assertAttributeEquals($attributes, '_attributesWhiteList', $validator);
        $this->assertEquals($validator, $result);
    }

    public function testSetAttributesBlackList()
    {
        $attributes = array('attr1', 'attr2', 'attr3');
        $validator = new Magento_Eav_Model_Validator_Attribute_Data;
        $result = $validator->setAttributesBlackList($attributes);
        $this->assertAttributeEquals($attributes, '_attributesBlackList', $validator);
        $this->assertEquals($validator, $result);
    }

    public function testSetAttributeDataModelFactory()
    {
        $factory = $this->getMockBuilder('Magento_Eav_Model_Attribute_Data')->getMock();
        $validator = new Magento_Eav_Model_Validator_Attribute_Data;
        $result = $validator->setAttributeDataModelFactory($factory);
        $this->assertAttributeEquals($factory, '_dataModelFactory', $validator);
        $this->assertEquals($validator, $result);
    }

    public function testGetAttributeDataModelFactory()
    {
        $validator = new Magento_Eav_Model_Validator_Attribute_Data;
        $factory = $validator->getAttributeDataModelFactory();
        $this->assertInstanceOf('Magento_Eav_Model_Attribute_Data', $factory);
        $this->assertAttributeEquals($factory, '_dataModelFactory', $validator);
    }

    public function testAddErrorMessages()
    {
        $data = array(
            'attribute1' => 'new_test',
            'attribute2' => 'some data'
        );
        $entity = $this->_getEntityMock();
        $firstAttribute = $this->_getAttributeMock(array(
            'attribute_code' => 'attribute1',
            'data_model' => $firstDataModel = $this->_getDataModelMock(array('Error1')),
            'frontend_input' => 'text'
        ));
        $secondAttribute = $this->_getAttributeMock(array(
            'attribute_code' => 'attribute2',
            'data_model' => $secondDataModel = $this->_getDataModelMock(array('Error2')),
            'frontend_input' => 'text'
        ));
        $expectedMessages = array(
            'attribute1' => array('Error1'),
            'attribute2' => array('Error2'),
        );
        $expectedDouble = array(
            'attribute1' => array('Error1', 'Error1'),
            'attribute2' => array('Error2', 'Error2'),
        );

        $validator = new Magento_Eav_Model_Validator_Attribute_Data;
        $validator->setAttributes(array($firstAttribute, $secondAttribute))
            ->setData($data);

        $factory = $this->getMockBuilder('Magento_Eav_Model_Attribute_Data')
            ->setMethods(array('factory'))
            ->getMock();
        $factory::staticExpects($this->at(0))
            ->method('factory')
            ->with($firstAttribute, $entity)
            ->will($this->returnValue($firstDataModel));
        $factory::staticExpects($this->at(1))
            ->method('factory')
            ->with($secondAttribute, $entity)
            ->will($this->returnValue($secondDataModel));
        $factory::staticExpects($this->at(2))
            ->method('factory')
            ->with($firstAttribute, $entity)
            ->will($this->returnValue($firstDataModel));
        $factory::staticExpects($this->at(3))
            ->method('factory')
            ->with($secondAttribute, $entity)
            ->will($this->returnValue($secondDataModel));

        $validator->setAttributeDataModelFactory($factory);

        $this->assertFalse($validator->isValid($entity));
        $this->assertEquals($expectedMessages, $validator->getMessages());
        $this->assertFalse($validator->isValid($entity));
        $this->assertEquals($expectedDouble, $validator->getMessages());
    }

    /**
     * @param array $attributeData
     * @return PHPUnit_Framework_MockObject_MockObject
     */
    protected function _getAttributeMock($attributeData)
    {
        $attribute = $this->getMockBuilder('Magento_Eav_Model_Attribute')
            ->setMethods(array('getAttributeCode', 'getDataModel', 'getFrontendInput'))
            ->disableOriginalConstructor()
            ->getMock();
        if (isset($attributeData['attribute_code'])) {
            $attribute->expects($this->any())->method('getAttributeCode')
                ->will($this->returnValue($attributeData['attribute_code']));
        }
        if (isset($attributeData['data_model'])) {
            $attribute->expects($this->any())->method('getDataModel')
                ->will($this->returnValue($attributeData['data_model']));
        }
        if (isset($attributeData['frontend_input'])) {
            $attribute->expects($this->any())->method('getFrontendInput')
                ->will($this->returnValue($attributeData['frontend_input']));
        }
        return $attribute;
    }

    /**
     * @param Magento_Eav_Model_Attribute_Data_Abstract $dataModel
     * @return PHPUnit_Framework_MockObject_MockObject
     */
    protected function _getFactoryMock($dataModel)
    {
        $factory = $this->getMockBuilder('Magento_Eav_Model_Attribute_Data')
            ->setMethods(array('factory'))
            ->getMock();
        $factory::staticExpects($this->once())
            ->method('factory')
            ->will($this->returnValue($dataModel));
        return $factory;
    }

    /**
     * @param boolean $returnValue
     * @param string|null $argument
     * @return PHPUnit_Framework_MockObject_MockObject
     */
    protected function _getDataModelMock($returnValue, $argument = null)
    {
        $dataModel = $this->getMockBuilder('Magento_Eav_Model_Attribute_Data_Abstract')
            ->disableOriginalConstructor()
            ->setMethods(array('validateValue'))
            ->getMockForAbstractClass();
        if ($argument) {
            $dataModel->expects($this->once())
                ->method('validateValue')
                ->with($argument)
                ->will($this->returnValue($returnValue));
        } else {
            $dataModel->expects($this->any())
                ->method('validateValue')
                ->will($this->returnValue($returnValue));
        }
        return $dataModel;
    }

    /**
     * @return PHPUnit_Framework_MockObject_MockObject
     */
    protected function _getEntityMock()
    {
        $entity = $this->getMockBuilder('Magento_Core_Model_Abstract')
            ->setMethods(array('getAttribute', 'getResource', 'getEntityType'))
            ->disableOriginalConstructor()
            ->getMock();
        return $entity;
    }
}
