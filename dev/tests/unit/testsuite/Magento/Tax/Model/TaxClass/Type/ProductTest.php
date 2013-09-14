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

class Magento_Tax_Model_TaxClass_Type_ProductTest extends PHPUnit_Framework_TestCase
{
    public function testGetAssignedObjects()
    {
        $collectionMock = $this->getMockBuilder('Magento\Core\Model\Resource\Db\Collection\AbstractCollection')
            ->setMethods(array(
                'addAttributeToFilter'
            ))
            ->disableOriginalConstructor()
            ->getMock();
        $collectionMock->expects($this->once())
            ->method('addAttributeToFilter')
            ->with($this->equalTo('tax_class_id'), $this->equalTo(1))
            ->will($this->returnSelf());

        $productMock = $this->getMockBuilder('Magento\Catalog\Model\Product')
            ->setMethods(array('getCollection'))
            ->disableOriginalConstructor()
            ->getMock();
        $productMock->expects($this->once())
            ->method('getCollection')
            ->will($this->returnValue($collectionMock));

        $objectManagerHelper = new Magento_TestFramework_Helper_ObjectManager($this);
        /** @var $model \Magento\Tax\Model\TaxClass\Type\Product */
        $model = $objectManagerHelper->getObject(
            'Magento\Tax\Model\TaxClass\Type\Product',
            array(
                'modelProduct' => $productMock,
                'data' => array('id' => 1)
            )
        );
        $this->assertEquals($collectionMock, $model->getAssignedToObjects());
    }
}
