<?php
/**
 * {license_notice}
 *
 * @spi
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Banner\Test\Block;

use Mtf\Block\Block;

/**
 * Banners block
 * Banner block in Banner widget on frontend
 */
class Banners extends Block
{
    protected $bannerText = '.inner .banner';

    /**
     * Return Banner content
     *
     * @return array
     */
    public function getBannerText()
    {
        $banners = $this->_rootElement->find($this->bannerText)->getElements();
        $bannersText = [];
        foreach ($banners as $banner) {
            $bannersText[] = $banner->getText();
        }

        return $bannersText;
    }
}
