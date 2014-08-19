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
 */
class CmsRevisionEdit extends BackendPage
{
    const MCA = 'admin/cms_page_revision/edit';

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
        'revisionForm' => [
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
