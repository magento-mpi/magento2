<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Mage_Tax
 * @subpackage  unit_tests
 * @copyright   {copyright}
 * @license     {license_link}
 */

class Mage_Tax_Model_Class_Type_CustomerTest extends PHPUnit_Framework_TestCase
{
    public function testGetAssignedObjects()
    {
        $collectionMock = $this->getMockBuilder('Magento_Core_Model_Resource_Db_Collection_Abstract')
            ->setMethods(array(
                'addFieldToFilter'
            ))
            ->disableOriginalConstructor()
            ->getMock();
        $collectionMock->expects($this->once())
            ->method('addFieldToFilter')
            ->with($this->equalTo('tax_class_id'), $this->equalTo(5))
            ->will($this->returnSelf());

        $customerGroupMock = $this->getMockBuilder('Mage_Customer_Model_Group')
            ->setMethods(array('getCollection'))
            ->disableOriginalConstructor()
            ->getMock();
        $customerGroupMock->expects($this->once())
            ->method('getCollection')
            ->will($this->returnValue($collectionMock));

        $objectManagerHelper = new Magento_Test_Helper_ObjectManager($this);
        /** @var $model Mage_Tax_Model_Class_Type_Customer */
        $model = $objectManagerHelper->getObject(
            'Mage_Tax_Model_Class_Type_Customer',
            array(
                'modelCustomerGroup' => $customerGroupMock,
                'helper' => $this->getMock('Mage_Tax_Helper_Data', array(), array(), '', false),
                'data' => array('id' => 5)
            )
        );
        $this->assertEquals($collectionMock, $model->getAssignedToObjects());
    }

}
