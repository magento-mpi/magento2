<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Checkout\Test\Block\Onepage;

use Mtf\Block\Block;

/**
 * Class Title
 * One page checkout success title block
 */
class Title extends Block
{

    /**
     * Get success message
     *
     * @return array|string
     */
    public function getTitle()
    {
        return $this->_rootElement->getText();
    }
}
