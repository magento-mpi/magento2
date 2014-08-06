<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\ConfigurableProduct\Test\Block\Adminhtml\Product;

use Mtf\Client\Element\Locator;
use Mtf\Fixture\FixtureInterface;
use Magento\Catalog\Test\Block\Adminhtml\Product\FormPageActions as CatalogFromPageActions;

class FormPageActions extends CatalogFromPageActions {
    /**
     * Selector for "Affected Attribute Set" popup form
     *
     * @var string
     */
    protected $affectedAttributeSetForm = '//ancestor::body//div[div[@id="affected-attribute-set-form"]]';

    /**
     * Click on "Save" button
     *
     * @param FixtureInterface|null $product [optional]
     * @return void
     */
    public function save(FixtureInterface $product = null)
    {
        parent::save();
        $affectedAttributeSetForm = $this->getAffectedAttributeSetForm();
        if ($affectedAttributeSetForm->isVisible()) {
            $affectedAttributeSetForm->fill($product)->confirm();
        }
    }

    /**
     * Get block of variations
     *
     * @return \Magento\ConfigurableProduct\Test\Block\Adminhtml\Product\AffectedAttributeSet
     */
    public function getAffectedAttributeSetForm()
    {
        return $this->blockFactory->create(
            '\Magento\ConfigurableProduct\Test\Block\Adminhtml\Product\AffectedAttributeSet',
            ['element' => $this->_rootElement->find($this->affectedAttributeSetForm, Locator::SELECTOR_XPATH)]
        );
    }
}

