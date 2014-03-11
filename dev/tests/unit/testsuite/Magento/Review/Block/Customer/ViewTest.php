<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Review\Block\Customer;

use Magento\TestFramework\Helper\ObjectManager as ObjectManagerHelper;

class ViewTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var \Magento\Review\Block\Customer\View
     */
    protected $model;

    /**
     * @var \Magento\Customer\Service\V1\CustomerCurrentServiceInterface|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $currentCustomer;

    /**
     * @var \Magento\App\RequestInterface|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $request;

    protected function setUp()
    {
        $this->request = $this->getMockForAbstractClass('Magento\App\RequestInterface');
        $this->currentCustomer = $this
            ->getMockForAbstractClass('Magento\Customer\Service\V1\CustomerCurrentServiceInterface');

        $objectManagerHelper = new ObjectManagerHelper($this);
        $this->model = $objectManagerHelper->getObject(
            'Magento\Review\Block\Customer\View',
            [
                'currentCustomer' => $this->currentCustomer,
                'request' => $this->request,
            ]
        );
    }

    /**
     * @param string $reviewCustomerId
     * @param string $currentCustomerId
     * @dataProvider isReviewOwnerDataProvider
     */
    public function testIsReviewOwner($reviewCustomerId, $currentCustomerId)
    {
        $this->model->setReviewCachedData(new \Magento\Object(['customer_id' => $reviewCustomerId]));
        $this->currentCustomer->expects($this->once())
            ->method('getCustomerId')
            ->will($this->returnValue($currentCustomerId));
        $this->assertEquals($reviewCustomerId == $currentCustomerId, $this->model->isReviewOwner());
    }

    public function isReviewOwnerDataProvider()
    {
        return [['review customer id', 'review customer id'], ['review customer id', 'current customer id']];
    }
}
