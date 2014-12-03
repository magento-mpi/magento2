<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Backend\Controller\Adminhtml\Dashboard;

/**
 * Test for \Magento\Backend\Controller\Adminhtml\Dashboard\CustomersMost
 */
class CustomersMostTest extends BaseAssertion
{
    public function testExecute()
    {
        $this->assertExecute(
            'Magento\Backend\Controller\Adminhtml\Dashboard\CustomersMost',
            'Magento\Backend\Block\Dashboard\Tab\Customers\Most'
        );
    }
}