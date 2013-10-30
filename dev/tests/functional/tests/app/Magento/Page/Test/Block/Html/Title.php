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

namespace Magento\Page\Test\Block\Html;

use Mtf\Block\Block;

/**
 * Page title block
 *
 * @package Magento\Page\Test\Block\Html
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
