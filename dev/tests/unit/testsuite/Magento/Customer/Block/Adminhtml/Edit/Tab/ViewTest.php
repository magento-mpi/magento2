<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Customer\Block\Adminhtml\Edit\Tab;

use \Magento\TestFramework\Helper\ObjectManager as ObjectManagerHelper;

/**
 * Class ViewTest
 * @package Magento\Customer\Block\Adminhtml\Edit\Tab
 */
class ViewTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var \Magento\Customer\Block\Adminhtml\Edit\Tab\View
     */
    protected $view;

    protected function setUp()
    {
        $customerAccountService = $this->getMock('Magento\Customer\Service\V1\CustomerAccountServiceInterface');
        $customerAddressService = $this->getMock('Magento\Customer\Service\V1\CustomerAddressServiceInterface');
        $customerGroupService = $this->getMock('Magento\Customer\Service\V1\CustomerGroupServiceInterface');
        $customerBuilder = $this->getMock('Magento\Customer\Service\V1\Data\CustomerBuilder', [], [], '', false);
        $customerAddressHelper = $this->getMock('Magento\Customer\Helper\Address', [], [], '', false);
        $registry = $this->getMock('Magento\Framework\Registry');

        $objectManagerHelper = new ObjectManagerHelper($this);
        $this->view = $objectManagerHelper->getObject(
            'Magento\Customer\Block\Adminhtml\Edit\Tab\View',
            [
                'accountService' => $customerAccountService,
                'addressService' => $customerAddressService,
                'groupService' => $customerGroupService,
                'customerBuilder' => $customerBuilder,
                'addressHelper' => $customerAddressHelper,
                'registry' => $registry
            ]
        );
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
