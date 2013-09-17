<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Tax
 * @subpackage  unit_tests
 * @copyright   {copyright}
 * @license     {license_link}
 */

class Magento_Tax_Model_Class_Type_ProductTest extends PHPUnit_Framework_TestCase
{
    public function testGetAssignedObjects()
    {
        $collectionMock = $this->getMockBuilder('Magento_Core_Model_Resource_Db_Collection_Abstract')
            ->setMethods(array(
                'addAttributeToFilter'
            ))
            ->disableOriginalConstructor()
            ->getMock();
        $collectionMock->expects($this->once())
            ->method('addAttributeToFilter')
            ->with($this->equalTo('tax_class_id'), $this->equalTo(1))
            ->will($this->returnSelf());

        $productMock = $this->getMockBuilder('Magento_Catalog_Model_Product')
            ->setMethods(array('getCollection'))
            ->disableOriginalConstructor()
            ->getMock();
        $productMock->expects($this->once())
            ->method('getCollection')
            ->will($this->returnValue($collectionMock));

        $objectManagerHelper = new Magento_TestFramework_Helper_ObjectManager($this);
        /** @var $model Magento_Tax_Model_Class_Type_Product */
        $model = $objectManagerHelper->getObject(
            'Magento_Tax_Model_Class_Type_Product',
            array(
                'modelProduct' => $productMock,
                'data' => array('id' => 1)
            )
        );
        $this->assertEquals($collectionMock, $model->getAssignedToObjects());
    }
}
