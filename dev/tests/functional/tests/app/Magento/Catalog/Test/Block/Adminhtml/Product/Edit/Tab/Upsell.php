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

namespace Magento\Catalog\Test\Block\Adminhtml\Product\Edit\Tab;

use Mtf\Client\Element;
use Mtf\Client\Element\Locator;

class Upsell extends \Magento\Backend\Test\Block\Widget\Grid
{
    /**
     * Tab where upsells section is placed
     */
    const GROUP_UPSELL = 'product_info_tabs_upsell';

    /**
     * Open upsells section
     *
     * @param Element $context
     */
    public function open(Element $context = null)
    {
        $element = $context ? $context : $this->_rootElement;
        $element->find(static::GROUP_UPSELL, Locator::SELECTOR_ID)->click();
    }
}
