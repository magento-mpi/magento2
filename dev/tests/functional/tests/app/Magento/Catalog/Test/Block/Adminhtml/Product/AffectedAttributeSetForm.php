<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Catalog\Test\Block\Adminhtml\Product;

use Mtf\Client\Element;
use Mtf\Client\Element\Locator;
use Mtf\Fixture\FixtureInterface;
use Magento\Backend\Test\Block\Widget\Form;

/**
 * Class AffectedAttributeSet
 * Choose affected attribute set dialog popup window
 */
class AffectedAttributeSetForm extends Form
{
    /**
     * 'Confirm' button locator
     *
     * @var string
     */
    protected $confirmButton = '//parent::div[div[@id="affected-attribute-set-form"]]//button[contains(@id,"confirm-button")]';

    /**
     * Locator buttons new name attribute set
     *
     * @var string
     */
    protected $affectedAttributeSetNew = '#affected-attribute-set-new';

    /**
     * Fill popup form
     *
     * @param FixtureInterface $product
     * @param Element $element
     * @return $this
     */
    public function fill(FixtureInterface $product, Element $element = null)
    {
        $data = $product->getData('affect_configurable_product_attributes');
        if (!empty($data)) {
            $this->_rootElement->find($this->affectedAttributeSetNew)->click();
            $fields = ['new_attribute_set_name' => strval($data)];
            $mapping = $this->dataMapping($fields);
            $this->_fill($mapping, $element);
        }
        return $this;
    }

    /**
     * Click confirm button
     *
     * @return void
     */
    public function confirm() {
        if ($this->_rootElement->find($this->confirmButton, Locator::SELECTOR_XPATH)->isVisible()) {
            $this->_rootElement->find($this->confirmButton, Locator::SELECTOR_XPATH)->click();
        }
    }

    /**
     * Save the form
     * (not used on this action)
     *
     * @param FixtureInterface $fixture
     * @return Form
     */
    public function save(FixtureInterface $fixture = null)
    {
        return $this;
    }
}
