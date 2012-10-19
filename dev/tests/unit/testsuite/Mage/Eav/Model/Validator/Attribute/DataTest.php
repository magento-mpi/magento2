<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Mage_Eav
 * @subpackage  unit_tests
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Test for Mage_Eav_Model_Validator_Attribute_Data
 */
class Mage_Eav_Model_Validator_Attribute_DataTest extends PHPUnit_Framework_TestCase
{
    /**
     * Testing  Mage_Eav_Model_Validator_Attribute_Data::isValid
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
        $entity = $this->_getEntityMock('test');
        $attribute = $this->_getAttributeMock($attributeData);

        $validator = new Mage_Eav_Model_Validator_Attribute_Data;
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
                    'data_model' => new Varien_Object(),
                    'frontend_input' => 'text'
                ),
                'attributeReturns' => true,
                'isValid' => true,
                'messages' => array()
            ),
            'is_invalid' => array(
                'attributeData' => array(
                    'attribute_code' => 'attribute',
                    'data_model' => new Varien_Object(),
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
                    'data_model' => new Varien_Object(),
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
                    'data_model' => new Varien_Object(),
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
     * Testing Mage_Eav_Model_Validator_Attribute_Data::isValid
     *
     * In this test entity attributes are got from attribute collection.
     */
    public function testIsValidAttributesFromCollection()
    {
        /** @var Mage_Eav_Model_Entity_Abstract $resource  */
        $resource = $this->getMockForAbstractClass('Mage_Eav_Model_Entity_Abstract');
        $attribute = $this->_getAttributeMock(array(
            'attribute_code' => 'attribute',
            'data_model' => new Varien_Object(),
            'frontend_input' => 'text'
        ));
        $collection = $this->getMockBuilder('Varien_Object')
            ->setMethods(array('getItems'))
            ->getMock();
        $collection->expects($this->once())->method('getItems')->will($this->returnValue(array($attribute)));
        $entityType = $this->getMockBuilder('Varien_Object')
            ->setMethods(array('getAttributeCollection'))
            ->getMock();
        $entityType->expects($this->once())->method('getAttributeCollection')->will($this->returnValue($collection));
        $entity = $this->_getEntityMock('test');
        $entity->expects($this->once())->method('getResource')->will($this->returnValue($resource));
        $entity->expects($this->once())->method('getEntityType')->will($this->returnValue($entityType));
        $dataModel = $this->_getDataModelMock(true);
        $factory = $this->_getFactoryMock($dataModel);

        $validator = new Mage_Eav_Model_Validator_Attribute_Data;
        $validator->setData(array('attribute' => 'new_test_data'))
            ->setAttributeDataModelFactory($factory);
        $this->assertTrue($validator->isValid($entity));
    }

    /**
     * @dataProvider whiteBlackListProvider
     * @param callback $callback
     */
    public function testIsValidBlackListChecks($callback)
    {
        $attribute = $this->_getAttributeMock(array(
            'attribute_code' => 'attribute',
            'data_model' => new Varien_Object(),
            'frontend_input' => 'text'
        ));
        $secondAttribute = $this->_getAttributeMock(array(
            'attribute_code' => 'attribute2',
            'data_model' => new Varien_Object(),
            'frontend_input' => 'text'
        ));
        $data = array(
            'attribute' => 'new_test_data',
            'attribute2' => 'some data'
        );
        $entity = $this->_getEntityMock('test');
        $dataModel = $this->_getDataModelMock(true, $data['attribute']);
        $factory = $this->_getFactoryMock($dataModel);

        $validator = new Mage_Eav_Model_Validator_Attribute_Data;
        $validator->setAttributeDataModelFactory($factory)
            ->setAttributes(array($attribute, $secondAttribute))
            ->setData($data);
        $callback($validator);
        $this->assertTrue($validator->isValid($entity));
    }

    public function whiteBlackListProvider()
    {
        return array(
            'white_list' => array(
                function ($validator) {
                    $validator->setAttributesWhiteList(array('attribute'));
                }
            ),
            'black_list' => array(
                function ($validator) {
                    $validator->setAttributesBlackList(array('attribute2'));
                }
            )
        );
    }

    public function testSetAttributesWhiteList()
    {
        $attributes = array('attr1', 'attr2', 'attr3');
        $validator = new Mage_Eav_Model_Validator_Attribute_Data;
        $result = $validator->setAttributesWhiteList($attributes);
        $this->assertAttributeEquals($attributes, '_attributesWhiteList', $validator);
        $this->assertEquals($validator, $result);
    }

    public function testSetAttributesBlackList()
    {
        $attributes = array('attr1', 'attr2', 'attr3');
        $validator = new Mage_Eav_Model_Validator_Attribute_Data;
        $result = $validator->setAttributesBlackList($attributes);
        $this->assertAttributeEquals($attributes, '_attributesBlackList', $validator);
        $this->assertEquals($validator, $result);
    }

    /**
     * @param $attributeData
     * @return PHPUnit_Framework_MockObject_MockObject
     */
    protected function _getAttributeMock($attributeData)
    {
        $attribute = $this->getMockBuilder('Mage_Eav_Model_Attribute')
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
     * @param $dataModel
     * @return PHPUnit_Framework_MockObject_MockObject
     */
    protected function _getFactoryMock($dataModel)
    {
        $factory = $this->getMockBuilder('Mage_Eav_Model_Attribute_Data')
            ->setMethods(array('factory'))
            ->getMock();
        $factory::staticExpects($this->once())
            ->method('factory')
            ->will($this->returnValue($dataModel));
        return $factory;
    }

    /**
     * @param $returnValue
     * @param null $argument
     * @return PHPUnit_Framework_MockObject_MockObject
     */
    protected function _getDataModelMock($returnValue, $argument = null)
    {
        $dataModel = $this->getMockBuilder('Mage_Eav_Model_Attribute_Data_Abstract')
            ->disableOriginalConstructor()
            ->setMethods(array('validateValue'))
            ->getMockForAbstractClass();
        if ($argument) {
            $dataModel->expects($this->once())
                ->method('validateValue')
                ->with($argument)
                ->will($this->returnValue($returnValue));
        } else {
            $dataModel->expects($this->once())
                ->method('validateValue')
                ->will($this->returnValue($returnValue));
        }
        return $dataModel;
    }

    /**
     * @param $returnValue
     * @return PHPUnit_Framework_MockObject_MockObject
     */
    protected function _getEntityMock($returnValue)
    {
        $entity = $this->getMockBuilder('Mage_Core_Model_Abstract')
            ->setMethods(array('getAttribute', 'getResource', 'getEntityType'))
            ->disableOriginalConstructor()
            ->getMock();
        $entity->expects($this->any())->method('getAttribute')->will($this->returnValue($returnValue));
        return $entity;
    }
}
