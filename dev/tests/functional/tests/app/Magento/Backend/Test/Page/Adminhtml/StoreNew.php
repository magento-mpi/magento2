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
        'editForm' => [
            'name' => 'editForm',
            'class' => 'Magento\Backend\Test\Block\System\Store\Edit\Form',
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
     * @return \Magento\Backend\Test\Block\System\Store\Edit\Form
     */
    public function getEditForm()
    {
        return $this->getBlockInstance('editForm');
    }

    /**
     * @return \Magento\Core\Test\Block\Messages
     */
    public function getMessagesBlock()
    {
        return $this->getBlockInstance('messagesBlock');
    }
}
