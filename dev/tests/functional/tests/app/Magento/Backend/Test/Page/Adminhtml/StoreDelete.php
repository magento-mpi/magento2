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
 * Class StoreDelete
 * Backend Store delete page
 */
class StoreDelete extends BackendPage
{
    const MCA = 'admin/system_store/deleteStore';

    protected $_blocks = [
        'messagesBlock' => [
            'name' => 'messagesBlock',
            'class' => 'Magento\Core\Test\Block\Messages',
            'locator' => '#messages',
            'strategy' => 'css selector',
        ],
        'storeForm' => [
            'name' => 'form',
            'class' => 'Magento\Backend\Test\Block\System\Store\Delete\StoreForm',
            'locator' => '#edit_form',
            'strategy' => 'css selector',
        ],
        'formPageActions' => [
            'name' => 'formPageActions',
            'class' => 'Magento\Backend\Test\Block\System\Store\FormPageActions',
            'locator' => '.content-footer',
            'strategy' => 'css selector',
        ],
    ];

    /**
     * @return \Magento\Core\Test\Block\Messages
     */
    public function getMessagesBlock()
    {
        return $this->getBlockInstance('messagesBlock');
    }

    /**
     * @return \Magento\Backend\Test\Block\System\Store\Delete\StoreForm
     */
    public function getStoreForm()
    {
        return $this->getBlockInstance('storeForm');
    }

    /**
     * @return \Magento\Backend\Test\Block\System\Store\FormPageActions
     */
    public function getFormPageActions()
    {
        return $this->getBlockInstance('formPageActions');
    }
}
