<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Cms\Test\Page;

use Mtf\Page\FrontendPage;

/**
 * Class CmsPage
 */
class CmsPage extends FrontendPage
{
    const MCA = 'cms/page';

    /**
     * Blocks' config
     *
     * @var array
     */
    protected $blocks = [
        'cmsPageBlock' => [
            'class' => 'Magento\Cms\Test\Block\Page',
            'locator' => '.page.main',
            'strategy' => 'css selector',
        ],
    ];

    /**
     * @return \Magento\Cms\Test\Block\Page
     */
    public function getCmsPageBlock()
    {
        return $this->getBlockInstance('cmsPageBlock');
    }
}
