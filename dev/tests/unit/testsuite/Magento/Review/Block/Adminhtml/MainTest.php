<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Review\Block\Adminhtml;

use Magento\TestFramework\Helper\ObjectManager as ObjectManagerHelper;

class MainTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var \Magento\Review\Block\Adminhtml\Main
     */
    protected $model;

    /**
     * @var \Magento\Framework\App\RequestInterface|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $request;

    /**
     * @var \Magento\Customer\Service\V1\CustomerAccountServiceInterface|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $customerAccount;

    public function testConstruct()
    {
        $this->customerAccount = $this
            ->getMockForAbstractClass('Magento\Customer\Service\V1\CustomerAccountServiceInterface');
        $this->customerAccount->expects($this->once())
            ->method('getCustomer')
            ->with('customer id')
            ->will($this->returnValue(new \Magento\Framework\Object()));
        $this->request = $this->getMockForAbstractClass('Magento\Framework\App\RequestInterface');
        $this->request->expects($this->at(0))
            ->method('getParam')
            ->with('customerId', false)
            ->will($this->returnValue('customer id'));
        $this->request->expects($this->at(1))
            ->method('getParam')
            ->with('productId', false)
            ->will($this->returnValue(false));


        $objectManagerHelper = new ObjectManagerHelper($this);
        $this->model = $objectManagerHelper->getObject(
            'Magento\Review\Block\Adminhtml\Main',
            [
                'request' => $this->request,
                'customerAccount' => $this->customerAccount
            ]
        );
    }
}
