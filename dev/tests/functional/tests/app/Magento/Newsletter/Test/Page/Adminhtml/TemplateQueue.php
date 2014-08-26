<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Newsletter\Test\Page\Adminhtml;

use Mtf\Page\BackendPage;

/**
 * Class TemplateQueue
 */
class TemplateQueue extends BackendPage
{
    const MCA = 'newsletter/queue/edit';

    /**
     * Blocks' config
     *
     * @var array
     */
    protected $blocks = [
        'formPageActions' => [
            'class' => 'Magento\Newsletter\Test\Block\Adminhtml\Template\FormPageActions',
            'locator' => '.page-main-actions',
            'strategy' => 'css selector',
        ],
        'editForm' => [
            'class' => 'Magento\Backend\Test\Block\Widget\Form',
            'locator' => '#queue_edit_form',
            'strategy' => 'css selector',
        ],
        'messagesBlock' => [
            'class' => 'Magento\Core\Test\Block\Messages',
            'locator' => '#messages',
            'strategy' => 'css selector',
        ],
    ];

    /**
     * @return \Magento\Newsletter\Test\Block\Adminhtml\Template\FormPageActions
     */
    public function getFormPageActions()
    {
        return $this->getBlockInstance('formPageActions');
    }

    /**
     * @return \Magento\Backend\Test\Block\Widget\Form
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
