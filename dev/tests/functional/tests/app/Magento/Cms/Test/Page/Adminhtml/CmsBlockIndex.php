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
 * Class CmsBlockIndex
 * Cms Block Index page
 */
class CmsBlockIndex extends BackendPage
{
    const MCA = 'cms/block/index';

    protected $_blocks = [
        'blockGrid' => [
            'name' => 'blockGrid',
            'class' => 'Magento\Cms\Test\Block\Adminhtml\Block\BlockGrid',
            'locator' => '#cmsBlockGrid',
            'strategy' => 'css selector',
        ],
    ];

    /**
     * @return \Magento\Cms\Test\Block\Adminhtml\Block\BlockGrid
     */
    public function getBlockGrid()
    {
        return $this->getBlockInstance('blockGrid');
    }
}
