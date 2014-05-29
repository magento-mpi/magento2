<?php
/**
 * {license_notice}
 *
 * @spi
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\UrlRewrite\Test\Block;

use Mtf\Block\Block;
use Mtf\Client\Element\Locator;

/**
 * Class Selector
 * URL rewrite type selector
 */
class Selector extends Block
{
    /**
     * Select URL type
     *
     * @param string $urlrewriteType
     */
    public function selectType($urlrewriteType)
    {
        $this->_rootElement->find("[id=url-rewrite-option-select]", Locator::SELECTOR_CSS, 'select')
            ->setValue($urlrewriteType);
    }
}
