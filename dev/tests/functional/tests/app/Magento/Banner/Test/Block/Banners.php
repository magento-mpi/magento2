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
 *
 */
class Banners extends Block
{
    protected $bannerText = '.inner .banner';

    /**
     * Return Banner content
     */
    public function getBannerText()
    {
        $banners = $this->_rootElement->find($this->bannerText)->getElements();
        $bannersText = [];
        foreach ($banners as $banner) {
            if (!$banner->isVisible()) {
                throw new \Exception('Banner is not visible');
            }
            $bannersText[] = $banner->getText();
        }
        return $bannersText;
    }
}
