<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Tax\Model\TaxClass\Type;

class CustomerTest extends \PHPUnit_Framework_TestCase
{
    public function testIsAssignedToObjects()
    {
        $objectManagerHelper = new \Magento\TestFramework\Helper\ObjectManager($this);

        $searchResultsMock  = $this->getMockBuilder('Magento\Framework\Api\SearchResults')
            ->setMethods(['getItems'])
            ->disableOriginalConstructor()
            ->getMock();
        $searchResultsMock->expects($this->once())
            ->method('getItems')
            ->will($this->returnValue(['randomValue']));

        $filterBuilder = $objectManagerHelper
            ->getObject('\Magento\Framework\Api\FilterBuilder');
        $filterGroupBuilder = $objectManagerHelper
            ->getObject('\Magento\Framework\Api\Search\FilterGroupBuilder');
        $searchCriteriaBuilder = $objectManagerHelper->getObject(
            'Magento\Framework\Api\SearchCriteriaBuilder',
            [
                'filterGroupBuilder' => $filterGroupBuilder
            ]
        );
        $expectedSearchCriteria = $searchCriteriaBuilder
            ->addFilter([$filterBuilder->setField('tax_class_id')->setValue(5)->create()])
            ->create();

        $customerGroupServiceMock = $this->getMockBuilder('Magento\Customer\Service\V1\CustomerGroupService')
            ->setMethods(['searchGroups'])
            ->disableOriginalConstructor()
            ->getMock();
        $customerGroupServiceMock->expects($this->once())
            ->method('searchGroups')
            ->with($this->equalTo($expectedSearchCriteria))
            ->will($this->returnValue($searchResultsMock));

        /** @var $model \Magento\Tax\Model\TaxClass\Type\Customer */
        $model = $objectManagerHelper->getObject(
            'Magento\Tax\Model\TaxClass\Type\Customer',
            [
                'groupService' => $customerGroupServiceMock,
                'filterBuilder' => $filterBuilder,
                'searchCriteriaBuilder' => $searchCriteriaBuilder,
                'data' => ['id' => 5]
            ]
        );

        $this->assertTrue($model->isAssignedToObjects());
    }
}
