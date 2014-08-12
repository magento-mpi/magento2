<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Banner\Test\Page;

use Magento\Cms\Test\Page\CmsIndex;
use Mtf\Factory\Factory;

/**
 * Class BannerIndex
 * Home page for frontend containing Banner
 */
class BannerIndex extends CmsIndex
{
    /**
     * MCA exists here only to make the factory accessor method name unique
     */
    const MCA = 'cms/index/banner/';

    /**
     * Banners block
     */
    protected $bannersBlock = '.widget.banners';

    /**
     * Get banners
     *
     * @return \Magento\Banner\Test\Block\Banners
     */
    public function getBannersBlock()
    {
        return Factory::getBlockFactory()->getMagentoBannerBanners(
            $this->_browser->find($this->bannersBlock)
        );
    }
}
