<?php
/**
 * {license_notice}
 *
 * @spi
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Theme\Test\Block\Html;

use Mtf\Block\Block;

/**
 * Page title block
 */
class Title extends Block
{
    /**
     * Get title of current category
     *
     * @return string
     */
    public function getTitle()
    {
        return $this->_rootElement->getText();
    }
}
