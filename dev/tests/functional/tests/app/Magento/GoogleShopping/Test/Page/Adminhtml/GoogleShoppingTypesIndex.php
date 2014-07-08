<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\GoogleShopping\Test\Page\Adminhtml;

use Mtf\Page\BackendPage;

/**
 * Class GoogleShoppingTypesIndex
 */
class GoogleShoppingTypesIndex extends BackendPage
{
    const MCA = 'admin/googleshopping_types/index';

    protected $_blocks = [
        'pageActionsBlock' => [
            'name' => 'pageActionsBlock',
            'class' => 'Magento\Backend\Test\Block\GridPageActions',
            'locator' => '.page-main-actions',
            'strategy' => 'css selector',
        ],
        'grid' => [
            'name' => 'grid',
            'class' => 'Magento\GoogleShopping\Test\Block\Adminhtml\Types\Grid',
            'locator' => '.grid',
            'strategy' => 'css selector',
        ],
    ];

    /**
     * @return \Magento\Backend\Test\Block\GridPageActions
     */
    public function getPageActionsBlock()
    {
        return $this->getBlockInstance('pageActionsBlock');
    }

    /**
     * @return \Magento\GoogleShopping\Test\Block\Adminhtml\Types\Grid
     */
    public function getGrid()
    {
        return $this->getBlockInstance('grid');
    }
}
