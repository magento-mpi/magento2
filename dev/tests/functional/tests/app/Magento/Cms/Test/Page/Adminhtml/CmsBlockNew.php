<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Cms\Test\Page\Adminhtml;

use Mtf\Page\BackendPage;

/**
 * Class CmsBlockNew
 * Backend new CMS Block page
 */
class CmsBlockNew extends BackendPage
{
    const MCA = 'cms/block/new';

    protected $_blocks = [
        'formPageActions' => [
            'name' => 'formPageActions',
            'class' => 'Magento\Backend\Test\Block\FormPageActions',
            'locator' => '.page-main-actions',
            'strategy' => 'css selector',
        ],
        'cmsForm' => [
            'name' => 'editForm',
            'class' => 'Magento\Cms\Test\Block\Adminhtml\Block\Edit\CmsForm',
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
     * @return \Magento\Backend\Test\Block\FormPageActions
     */
    public function getFormPageActions()
    {
        return $this->getBlockInstance('formPageActions');
    }

    /**
     * @return \Magento\Cms\Test\Block\Adminhtml\Block\Edit\CmsForm
     */
    public function getCmsForm()
    {
        return $this->getBlockInstance('cmsForm');
    }

    /**
     * @return \Magento\Core\Test\Block\Messages
     */
    public function getMessagesBlock()
    {
        return $this->getBlockInstance('messagesBlock');
    }
}
