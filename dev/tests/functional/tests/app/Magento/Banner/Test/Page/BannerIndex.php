<?php
/**
 * {license_notice}
 *
 * @category    Mtf
 * @package     Mtf
 * @subpackage  functional_tests
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Banner\Test\Page;

use Magento\Cms\Test\Page\CmsIndex;
use Mtf\Factory\Factory;
use Mtf\Client\Element\Locator;

/**
 * Class BannerIndex
 * Home page for frontend containing Banner
 *
 * @package Magento\Banner\Test\Page
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
            $this->_browser->find($this->bannersBlock, Locator::SELECTOR_CSS)
        );
    }
}
