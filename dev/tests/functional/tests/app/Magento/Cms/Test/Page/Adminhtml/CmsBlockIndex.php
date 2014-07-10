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
        'grid' => [
            'name' => 'grid',
            'class' => 'Magento\Cms\Test\Block\Adminhtml\Block\Grid',
            'locator' => '#cmsBlockGrid',
            'strategy' => 'css selector',
        ],
    ];

    /**
     * @return \Magento\Cms\Test\Block\Adminhtml\Block\Grid
     */
    public function getGrid()
    {
        return $this->getBlockInstance('grid');
    }
}
