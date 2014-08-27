<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Customer\Block\Adminhtml\Edit\Tab;

use \Magento\TestFramework\Helper\ObjectManager as ObjectManagerHelper;

class ViewTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var \Magento\Customer\Block\Adminhtml\Edit\Tab\View
     */
    protected $view;

    /**
     * @var ObjectManagerHelper
     */
    protected $objectManagerHelper;

    /**
     * @var \Magento\Backend\Block\Template\Context|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $context;

    /**
     * @var \Magento\Customer\Service\V1\CustomerAccountServiceInterface|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $customerAccountServiceInterface;

    /**
     * @var \Magento\Customer\Service\V1\CustomerAddressServiceInterface|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $customerAddressServiceInterface;

    /**
     * @var \Magento\Customer\Service\V1\CustomerGroupServiceInterface|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $customerGroupServiceInterface;

    /**
     * @var \Magento\Customer\Service\V1\Data\CustomerBuilder|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $customerBuilder;

    /**
     * @var \Magento\Customer\Helper\Address|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $customerAddressHelper;

    /**
     * @var \Magento\Framework\Registry|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $registry;

    protected function setUp()
    {
        $this->context = $this->getMock('Magento\Backend\Block\Template\Context', [], [], '', false);
        $this->customerAccountServiceInterface = $this->getMock('Magento\Customer\Service\V1\CustomerAccountServiceInterface');
        $this->customerAddressServiceInterface = $this->getMock('Magento\Customer\Service\V1\CustomerAddressServiceInterface');
        $this->customerGroupServiceInterface = $this->getMock('Magento\Customer\Service\V1\CustomerGroupServiceInterface');
        $this->customerBuilder = $this->getMock('Magento\Customer\Service\V1\Data\CustomerBuilder', [], [], '', false);
        $this->customerAddressHelper = $this->getMock('Magento\Customer\Helper\Address', [], [], '', false);
        $this->registry = $this->getMock('Magento\Framework\Registry');

        $this->objectManagerHelper = new ObjectManagerHelper($this);
        $this->view = $this->objectManagerHelper->getObject(
            'Magento\Customer\Block\Adminhtml\Edit\Tab\View',
            [
                'context' => $this->context,
                'accountService' => $this->customerAccountServiceInterface,
                'addressService' => $this->customerAddressServiceInterface,
                'groupService' => $this->customerGroupServiceInterface,
                'customerBuilder' => $this->customerBuilder,
                'addressHelper' => $this->customerAddressHelper,
                'registry' => $this->registry
            ]
        );
    }

    public function testGetLastLoginDate()
    {
        $this->assertEquals('Never', $this->view->getLastLoginDate());
    }

    public function testGetStoreLastLoginDate()
    {
        $this->assertEquals('Never', $this->view->getStoreLastLoginDate());
    }

    public function testGetCurrentStatus()
    {
        $this->assertEquals('Offline', $this->view->getCurrentStatus());
    }

    public function testGetTabLabel()
    {
        $this->assertEquals('Customer View', $this->view->getTabLabel());
    }

    public function testGetTabTitle()
    {
        $this->assertEquals('Customer View', $this->view->getTabTitle());
    }
}
