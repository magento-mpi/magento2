<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Backend\Test\Block;

use Mtf\Block\Block;
use Mtf\Client\Element\Locator;

/**
 * Class Denied
 * Access Denied Block
 *
 */
class Denied extends Block
{
    /**
     * Block with "Access Denied Text"
     *
     * @var string
     */
    protected $accessDeniedText = ".page-heading";

    /**
     * Get comments history
     *
     * @return string
     */
    public function getTextFromAccessDeniedBlock()
    {
        return $this->_rootElement->find($this->accessDeniedText, Locator::SELECTOR_CSS)->getText();
    }
}

