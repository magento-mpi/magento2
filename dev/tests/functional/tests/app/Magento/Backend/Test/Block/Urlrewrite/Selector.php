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

namespace Magento\Backend\Test\Block\Urlrewrite;

use Mtf\Block\Block,
    Mtf\Client\Element\Locator;

/**
 * Class Selector
 * URL rewrite type selector
 *
 * @package Magento\Backend\Test\Block\Urlrewrite
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
