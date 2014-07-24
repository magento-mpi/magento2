<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Backend\Test\Page\Adminhtml;

use Mtf\Page\BackendPage;

/**
 * Class StoreNew
 * Backend new Store page
 */
class StoreNew extends BackendPage
{
    const MCA = 'admin/system_store/newStore';

    protected $_blocks = [
        'formPageActions' => [
            'name' => 'formPageActions',
            'class' => 'Magento\Backend\Test\Block\System\Store\FormPageActions',
            'locator' => '.page-main-actions',
            'strategy' => 'css selector',
        ],
        'storeForm' => [
            'name' => 'storeForm',
            'class' => 'Magento\Backend\Test\Block\System\Store\Edit\StoreForm',
            'locator' => '[id="page:main-container"]',
            'strategy' => 'css selector',
        ],
        'messagesBlock' => [
            'name' => 'messagesBlock',
            'class' => 'Magento\Core\Test\Block\Messages',
            'locator' => '#messages',
            'strategy' => 'css selector',
        ],
    ];

    /**
     * @return \Magento\Backend\Test\Block\System\Store\FormPageActions
     */
    public function getFormPageActions()
    {
        return $this->getBlockInstance('formPageActions');
    }

    /**
     * @return \Magento\Backend\Test\Block\System\Store\Edit\StoreForm
     */
    public function getStoreForm()
    {
        return $this->getBlockInstance('storeForm');
    }

    /**
     * @return \Magento\Core\Test\Block\Messages
     */
    public function getMessagesBlock()
    {
        return $this->getBlockInstance('messagesBlock');
    }
}
