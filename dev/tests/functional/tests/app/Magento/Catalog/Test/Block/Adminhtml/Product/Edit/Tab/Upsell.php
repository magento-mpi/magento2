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
use Mtf\Factory\Factory;
use Mtf\Client\Element\Locator;
use Magento\Backend\Test\Block\Widget\Tab;

class Upsell extends Tab
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
        // @var Mtf\Client\Element
        $element->find(static::GROUP_UPSELL, Locator::SELECTOR_ID)->click();
    }
}