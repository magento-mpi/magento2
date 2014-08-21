<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\VersionsCms\Test\Page\Adminhtml;

use Mtf\Page\BackendPage;

/**
 * Class CmsRevisionEdit
 * Cms Page Revision Edit on backend
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
        'revisionForm' => [
            'name' => 'revisionForm',
            'class' => 'Magento\VersionsCms\Test\Block\Adminhtml\Cms\Page\Revision\Edit\RevisionForm',
            'locator' => '#edit_form',
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
     * @return \Magento\VersionsCms\Test\Block\Adminhtml\Cms\Page\Revision\Edit\RevisionForm
     */
    public function getRevisionForm()
    {
        return $this->getBlockInstance('revisionForm');
    }
}
