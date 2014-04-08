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

class CustomerTest extends \PHPUnit_Framework_TestCase
{
    public function testGetAssignedObjects()
    {
        $this->markTestSkipped('Need to be updated');
        $objectManagerHelper = new \Magento\TestFramework\Helper\ObjectManager($this);

        $collectionMock = $objectManagerHelper->getObject('Magento\Tax\Model\TaxClass\Type\Customer');
        $collectionMock->expects(
            $this->once()
        )->method(
            'addFieldToFilter'
        )->with(
            $this->equalTo('tax_class_id'),
            $this->equalTo(5)
        )->will(
            $this->returnSelf()
        );

        $customerGroupMock = $this->getMockBuilder(
            'Magento\Customer\Model\Group'
        )->setMethods(
            array('getCollection', '__wakeup')
        )->disableOriginalConstructor()->getMock();
        $customerGroupMock->expects($this->once())->method('getCollection')->will($this->returnValue($collectionMock));

        /** @var $model \Magento\Tax\Model\TaxClass\Type\Customer */
        $model = $objectManagerHelper->getObject(
            'Magento\Tax\Model\TaxClass\Type\Customer',
            array('modelCustomerGroup' => $customerGroupMock, 'data' => array('id' => 5))
        );
        $this->assertEquals(1, $model->getAssignedToObjectsSize());
    }
}
