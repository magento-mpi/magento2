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
 */
class StoreDelete extends BackendPage
{
    const MCA = 'admin/system_store/deleteStore';

    /**
     * Blocks' config
     *
     * @var array
     */
    protected $blocks = [
        'messagesBlock' => [
            'class' => 'Magento\Core\Test\Block\Messages',
            'locator' => '#messages',
            'strategy' => 'css selector',
        ],
        'storeForm' => [
            'class' => 'Magento\Backend\Test\Block\System\Store\Delete\Form',
            'locator' => '#edit_form',
            'strategy' => 'css selector',
        ],
        'formPageFooterActions' => [
            'class' => 'Magento\Backend\Test\Block\System\Store\FormPageFooterActions',
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
     * @return \Magento\Backend\Test\Block\System\Store\Delete\Form
     */
    public function getStoreForm()
    {
        return $this->getBlockInstance('storeForm');
    }

    /**
     * @return \Magento\Backend\Test\Block\System\Store\FormPageFooterActions
     */
    public function getFormPageFooterActions()
    {
        return $this->getBlockInstance('formPageFooterActions');
    }
}
