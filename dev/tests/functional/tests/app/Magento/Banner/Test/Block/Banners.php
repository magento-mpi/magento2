<?php
/**
 * {license_notice}
 *
 * @spi
 * @category    Mtf
 * @package     Mtf
 * @subpackage  functional_tests
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Banner\Test\Block;

use Mtf\Block\Block;
use Mtf\Client\Element\Locator;

/**
 * Banners block
 *
 * @package Magento\Banner\Test\Block
 */
class Banners extends Block
{
    protected $banner_text = '.inner .banner';

    /**
     * Return Banner content
     */
    public function getBannerText()
    {
        return $this->_rootElement->find($this->banner_text, Locator::SELECTOR_CSS)->getText();
    }
}
