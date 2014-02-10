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

namespace Magento\Catalog\Test\Block\Backend\Product\Attribute\MassAction;

use Mtf\Fixture;
use Mtf\Client\Element;
use Magento\Backend\Test\Block\Widget\Form;

/**
 * Product attribute massaction edit page
 * @package Magento\Catalog\Test\Block\Backend\Product\Attribute\MassAction
 */
class Edit extends Form
{
    /**
     * @var string
     */
    protected $saveButton = '[data-ui-id="attribute-save-button"]';

    /**
     * @var string
     */
    protected $_priceFieldEnablerSelector = '//*[@id="attribute-price-container"]/div[1]/div/label/span';

    /**
     * Enable price field editing
     */
    public function enablePriceEdit()
    {
        $this->_rootElement->find($this->_priceFieldEnablerSelector, Element\Locator::SELECTOR_XPATH)->click();
    }
}
