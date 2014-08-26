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

    /**
     * Blocks' config
     *
     * @var array
     */
    protected $blocks = [
        'pageActionsBlock' => [
            'class' => 'Magento\Backend\Test\Block\GridPageActions',
            'locator' => '.page-main-actions',
            'strategy' => 'css selector',
        ],
        'grid' => [
            'class' => 'Magento\Backend\Test\Block\Widget\Grid',
            'locator' => '#types_grid',
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
     * @return \Magento\Backend\Test\Block\Widget\Grid
     */
    public function getGrid()
    {
        return $this->getBlockInstance('grid');
    }
}
