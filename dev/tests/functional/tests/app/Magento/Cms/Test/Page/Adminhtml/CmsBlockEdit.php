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
 *
 * @package Magento\Cms\Test\Page\Adminhtml
 */
class CmsBlockEdit extends BackendPage
{
    const MCA = 'cms/block/edit/block_id/1/';

    protected $_blocks = [
        'blockForm' => [
            'name' => 'blockForm',
            'class' => 'Magento\Cms\Test\Block\Adminhtml\Block\Edit\BlockForm',
            'locator' => '[id="page:main-container"]',
            'strategy' => 'css selector',
        ],
        'pageMainActions' => [
            'name' => 'pageMainActions',
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
