<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\GiftWrapping\Test\Page\Adminhtml;

use Mtf\Page\BackendPage;

/**
 * Class GiftWrappingNew
 */
class GiftWrappingNew extends BackendPage
{
    const MCA = 'admin/giftwrapping/new/index';

    /**
     * Blocks' config
     *
     * @var array
     */
    protected $blocks = [
        'formPageActions' => [
            'class' => 'Magento\Backend\Test\Block\FormPageActions',
            'locator' => '.page-main-actions',
            'strategy' => 'css selector',
        ],
        'giftWrappingForm' => [
            'class' => 'Magento\GiftWrapping\Test\Block\Adminhtml\Edit\Form',
            'locator' => '#edit_form',
            'strategy' => 'css selector',
        ],
        'messagesBlock' => [
            'class' => 'Magento\Core\Test\Block\Messages',
            'locator' => '#messages',
            'strategy' => 'css selector',
        ],
    ];

    /**
     * @return \Magento\Backend\Test\Block\FormPageActions
     */
    public function getFormPageActions()
    {
        return $this->getBlockInstance('formPageActions');
    }

    /**
     * @return \Magento\GiftWrapping\Test\Block\Adminhtml\Edit\Form
     */
    public function getGiftWrappingForm()
    {
        return $this->getBlockInstance('giftWrappingForm');
    }

    /**
     * @return \Magento\Core\Test\Block\Messages
     */
    public function getMessagesBlock()
    {
        return $this->getBlockInstance('messagesBlock');
    }
}
