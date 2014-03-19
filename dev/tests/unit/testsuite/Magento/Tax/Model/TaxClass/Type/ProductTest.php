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
namespace Magento\Tax\Model\TaxClass\Type;

class ProductTest extends \PHPUnit_Framework_TestCase
{
    public function testGetAssignedObjects()
    {
        $collectionMock = $this->getMockBuilder(
            'Magento\Core\Model\Resource\Db\Collection\AbstractCollection'
        )->setMethods(
            array('addAttributeToFilter')
        )->disableOriginalConstructor()->getMock();
        $collectionMock->expects(
            $this->once()
        )->method(
            'addAttributeToFilter'
        )->with(
            $this->equalTo('tax_class_id'),
            $this->equalTo(1)
        )->will(
            $this->returnSelf()
        );

        $productMock = $this->getMockBuilder(
            'Magento\Catalog\Model\Product'
        )->setMethods(
            array('getCollection', '__wakeup')
        )->disableOriginalConstructor()->getMock();
        $productMock->expects($this->once())->method('getCollection')->will($this->returnValue($collectionMock));

        $objectManagerHelper = new \Magento\TestFramework\Helper\ObjectManager($this);
        /** @var $model \Magento\Tax\Model\TaxClass\Type\Product */
        $model = $objectManagerHelper->getObject(
            'Magento\Tax\Model\TaxClass\Type\Product',
            array('modelProduct' => $productMock, 'data' => array('id' => 1))
        );
        $this->assertEquals($collectionMock, $model->getAssignedToObjects());
    }
}
