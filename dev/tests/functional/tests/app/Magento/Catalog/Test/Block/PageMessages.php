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

namespace Magento\Catalog\Test\Block;

use Mtf\Block\Block;
use Mtf\Client\Element\Locator;

/**
 * Class PageMessages
 * Global messages for page
 *
 * @package Magento\Catalog\Test\Block
 */
class PageMessages extends Block
{
    /**
     * Get all success messages which are present on the page
     *
     * @return string
     */
    public function getSuccessMessages()
    {
        return $this->_rootElement
            ->find('[data-ui-id=messages-message-success]', Locator::SELECTOR_CSS)
            ->getText();
    }

    /**
     * Get all error messages which are present on the page
     */
    public function getErrorMessages()
    {
        //..
    }
}
