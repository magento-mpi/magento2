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
 * Class GoogleShoppingTypesNew
 */
class GoogleShoppingTypesNew extends BackendPage
{
    const MCA = 'admin/googleshopping_types/new';

    protected $_blocks = [
        'pageActions' => [
            'name' => 'pageActions',
            'class' => 'Magento\GoogleShopping\Test\Block\Adminhtml\Types\FormPageActions',
            'locator' => '.page-main-actions',
            'strategy' => 'css selector',
        ],
        'googleShoppingForm' => [
            'name' => 'googleShoppingForm',
            'class' => 'Magento\GoogleShopping\Test\Block\Adminhtml\Types\Edit\GoogleShoppingForm',
            'locator' => '#edit_form',
            'strategy' => 'css selector',
        ],
    ];

    /**
     * @return \Magento\GoogleShopping\Test\Block\Adminhtml\Types\FormPageActions
     */
    public function getPageActions()
    {
        return $this->getBlockInstance('pageActions');
    }

    /**
     * @return \Magento\GoogleShopping\Test\Block\Adminhtml\Types\Edit\GoogleShoppingForm
     */
    public function getGoogleShoppingForm()
    {
        return $this->getBlockInstance('googleShoppingForm');
    }
}
