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

namespace Magento\Catalog\Test\Block;

use Mtf\Block\Block;
use Mtf\Client\Element\Locator;

/**
 * Category title block
 *
 * @package Magento\Catalog\Test\Block
 */
class CategoryTitle extends Block
{
    /**
     * Get title of current category
     *
     * @return string
     */
    public function getCategoryTitle()
    {
        if($this->_rootElement->isVisible()) {
            return $this->_rootElement->getText();
        }
    }
}
