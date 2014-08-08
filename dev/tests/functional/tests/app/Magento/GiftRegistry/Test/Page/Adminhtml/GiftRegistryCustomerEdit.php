<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\GiftRegistry\Test\Page\Adminhtml;

use Mtf\Page\BackendPage;

/**
 * Class GiftRegistryCustomerEdit
 * Gift registry page on backend
 */
class GiftRegistryCustomerEdit extends BackendPage
{
    const MCA = 'giftregistry_customer/edit';

    protected $_blocks = [
        'actionsToolbarBlock' => [
            'name' => 'actionsToolbarBlock',
            'class' => 'Magento\GiftRegistry\Test\Block\Adminhtml\Edit\ActionsToolbar',
            'locator' => '.page-main-actions',
            'strategy' => 'css selector',
        ],
    ];

    /**
     * @return \Magento\GiftRegistry\Test\Block\Adminhtml\Edit\ActionsToolbar
     */
    public function getActionsToolbarBlock()
    {
        return $this->getBlockInstance('actionsToolbarBlock');
    }
}
