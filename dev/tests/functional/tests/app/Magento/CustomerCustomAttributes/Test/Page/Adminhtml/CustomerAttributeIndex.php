<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\CustomerCustomAttributes\Test\Page\Adminhtml;

use Mtf\Page\BackendPage;

/**
 * Class CustomerAttributeIndex
 */
class CustomerAttributeIndex extends BackendPage
{
    const MCA = 'admin/customer_attribute';

    // @codingStandardsIgnoreStart
    protected $_blocks = [
        'messagesBlock' => [
            'name' => 'messagesBlock',
            'class' => 'Magento\Core\Test\Block\Messages',
            'locator' => '#messages',
            'strategy' => 'css selector',
        ],
        'gridPageActions' => [
            'name' => 'gridPageActions',
            'class' => 'Magento\Backend\Test\Block\GridPageActions',
            'locator' => '.page-main-actions',
            'strategy' => 'css selector',
        ],
        'customerCustomAttributesGrid' => [
            'name' => 'customerCustomAttributesGrid',
            'class' => 'Magento\CustomerCustomAttributes\Test\Block\Adminhtml\Customer\Attribute\CustomerCustomAttributesGrid',
            'locator' => '[id="customerAttributeGrid"]',
            'strategy' => 'css selector',
        ],
    ];
    // @codingStandardsIgnoreEnd

    /**
     * @return \Magento\Core\Test\Block\Messages
     */
    public function getMessagesBlock()
    {
        return $this->getBlockInstance('messagesBlock');
    }

    /**
     * @return \Magento\Backend\Test\Block\GridPageActions
     */
    public function getGridPageActions()
    {
        return $this->getBlockInstance('gridPageActions');
    }

    /**
     * @return \Magento\CustomerCustomAttributes\Test\Block\Adminhtml\Customer\Attribute\CustomerCustomAttributesGrid
     */
    public function getCustomerCustomAttributesGrid()
    {
        return $this->getBlockInstance('customerCustomAttributesGrid');
    }
}
