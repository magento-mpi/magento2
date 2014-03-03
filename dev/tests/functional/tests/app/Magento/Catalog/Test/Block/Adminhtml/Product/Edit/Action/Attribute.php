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

namespace Magento\Catalog\Test\Block\Adminhtml\Product\Edit\Action;

use Mtf\Fixture;
use Mtf\Client\Element;
use Magento\Backend\Test\Block\Widget\Form;

/**
 * Product attribute massaction edit page
 *
 * @package Magento\Catalog\Test\Block\Adminhtml\Product\Edit\Action
 */
class Attribute extends Form
{
    /**
     * CSS selector for 'save' button
     *
     * @var string
     */
    protected $saveButton = '[data-ui-id="attribute-save-button"]';

    /**
     * XPath selector for checkbox that enables price editing
     *
     * @var string
     */
    protected $priceFieldEnablerSelector = '//*[@id="attribute-price-container"]/div[1]/div/label/span';

    /**
     * Enable price field editing
     */
    public function enablePriceEdit()
    {
        $this->_rootElement->find($this->priceFieldEnablerSelector, Element\Locator::SELECTOR_XPATH)->click();
    }
}
