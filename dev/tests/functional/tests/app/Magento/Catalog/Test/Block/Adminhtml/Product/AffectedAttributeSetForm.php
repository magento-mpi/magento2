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
use Magento\Backend\Test\Block\Widget\Form as ParentForm;

/**
 * Class AffectedAttributeSet
 * Choose affected attribute set dialog popup window
 */
class AffectedAttributeSetForm extends ParentForm
{
    /**
     * 'Confirm' button container locator
     *
     * @var string
     */
    protected $confirmButtonContainer = '//parent::div[div[@id="affected-attribute-set-form"]]';

    /**
     * 'Confirm' button locator
     *
     * @var string
     */
    protected $confirmButton = '//button[contains(@id,"confirm-button")]';

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
     * @param Element|null $element
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
    public function confirm()
    {
        $isVisible = $this->_rootElement->find(
            $this->confirmButtonContainer . $this->confirmButton,
            Locator::SELECTOR_XPATH
        )->isVisible();

        if ($isVisible) {
            $this->_rootElement->find(
                $this->confirmButtonContainer . $this->confirmButton,
                Locator::SELECTOR_XPATH
            )->click();
        }
    }
}
