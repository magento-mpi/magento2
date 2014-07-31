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
 * Class CmsRevisionEdit
 * Cms Revision page on backend
 */
class CmsRevisionEdit extends BackendPage
{
    const MCA = 'admin/cms_page_revision/edit';

    protected $_blocks = [
        'formPageActions' => [
            'name' => 'formPageActions',
            'class' => 'Magento\Backend\Test\Block\FormPageActions',
            'locator' => '.page-main-actions',
            'strategy' => 'css selector',
        ],
        'content' => [
            'name' => 'content',
            'class' => 'Magento\VersionsCms\Test\Block\Adminhtml\Cms\Page\Revision\Edit\Tab\Content',
            'locator' => '[id="content"]',
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
     * @return \Magento\VersionsCms\Test\Block\Adminhtml\Cms\Page\Revision\Edit\Tab\Content
     */
    public function getContent()
    {
        return $this->getBlockInstance('content');
    }
}
