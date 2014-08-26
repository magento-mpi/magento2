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
 * Class CmsBlockEdit
 */
class CmsBlockEdit extends BackendPage
{
    const MCA = 'cms/block/edit';

    /**
     * Blocks' config
     *
     * @var array
     */
    protected $blocks = [
        'blockForm' => [
            'class' => 'Magento\Cms\Test\Block\Adminhtml\Block\Edit\BlockForm',
            'locator' => '[id="page:main-container"]',
            'strategy' => 'css selector',
        ],
        'pageMainActions' => [
            'class' => 'Magento\Backend\Test\Block\FormPageActions',
            'locator' => '.page-main-actions',
            'strategy' => 'css selector',
        ],
    ];

    /**
     * @return \Magento\Cms\Test\Block\Adminhtml\Block\Edit\BlockForm
     */
    public function getBlockForm()
    {
        return $this->getBlockInstance('blockForm');
    }

    /**
     * @return \Magento\Backend\Test\Block\FormPageActions
     */
    public function getPageMainActions()
    {
        return $this->getBlockInstance('pageMainActions');
    }
}
