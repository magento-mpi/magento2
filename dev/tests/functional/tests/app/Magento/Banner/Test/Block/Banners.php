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
use Mtf\Client\Element\Locator;

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
        return $this->_rootElement->find($this->bannerText, Locator::SELECTOR_CSS)->getText();
    }
}
