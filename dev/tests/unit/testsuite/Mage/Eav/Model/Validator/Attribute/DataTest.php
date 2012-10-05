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
     * Test isValid
     *
     * @dataProvider isValidDataProvider
     *
     * @param $result
     * @param $expected
     * @param array $messages
     */
    public function testIsValid($result, $expected, $messages)
    {
        $entity = new Varien_Object(array('attribute' => 'test'));
        $attribute = new Varien_Object(array('attribute_code' => 'attribute'));
        $data = array('attribute' => 'new_test');

        $validator = $this->getMockBuilder('Mage_Eav_Model_Validator_Attribute_Data')
            ->setMethods(array('_getAttributeDataModel'))
            ->getMock();

        $validator->setAttributes(array($attribute));
        $validator->setData($data);

        $dataModel = $this->getMockBuilder('Mage_Eav_Model_Attribute_Data_Abstract')
            ->disableOriginalConstructor()
            ->setMethods(array('validateValue'))
            ->getMockForAbstractClass();
        $dataModel->expects($this->once())
            ->method('validateValue')
            ->will($this->returnValue($result));

        $validator->expects($this->once())
            ->method('_getAttributeDataModel')
            ->with($attribute, $entity)
            ->will($this->returnValue($dataModel));

        $this->assertEquals($expected, $validator->isValid($entity));
        $this->assertEquals($messages, $validator->getMessages());
    }

    /**
     * Date provider for testIsValid
     *
     * @return array
     */
    public function isValidDataProvider()
    {
        return array(
            'is_valid' => array(true, true, array()),
            'is_invalid' => array(array('Error'), false, array('attribute' => array('Error'))),
        );
    }
}
