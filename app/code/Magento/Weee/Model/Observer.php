<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Weee
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Weee\Model;

class Observer extends \Magento\Core\Model\AbstractModel
{
    /**
     * Assign custom renderer for product create/edit form weee attribute element
     *
     * @param \Magento\Event\Observer $observer
     * @return  \Magento\Weee\Model\Observer
     */
    public function setWeeeRendererInForm(\Magento\Event\Observer $observer)
    {
        //adminhtml_catalog_product_edit_prepare_form

        $form = $observer->getEvent()->getForm();
//        $product = $observer->getEvent()->getProduct();

        $attributes = \Mage::getSingleton('Magento\Weee\Model\Tax')->getWeeeAttributeCodes(true);
        foreach ($attributes as $code) {
            if ($weeeTax = $form->getElement($code)) {
                $weeeTax->setRenderer(
                    \Mage::app()->getLayout()->createBlock('Magento\Weee\Block\Renderer\Weee\Tax')
                );
            }
        }

        return $this;
    }

    /**
     * Exclude WEEE attributes from standard form generation
     *
     * @param \Magento\Event\Observer $observer
     * @return  \Magento\Weee\Model\Observer
     */
    public function updateExcludedFieldList(\Magento\Event\Observer $observer)
    {
        //adminhtml_catalog_product_form_prepare_excluded_field_list

        $block      = $observer->getEvent()->getObject();
        $list       = $block->getFormExcludedFieldList();
        $attributes = \Mage::getSingleton('Magento\Weee\Model\Tax')->getWeeeAttributeCodes(true);
        $list       = array_merge($list, array_values($attributes));

        $block->setFormExcludedFieldList($list);

        return $this;
    }

    /**
     * Get empty select object
     *
     * @return \Magento\DB\Select
     */
    protected function _getSelect()
    {
        return \Mage::getSingleton('Magento\Weee\Model\Tax')->getResource()->getReadConnection()->select();
    }

    /**
     * Add new attribute type to manage attributes interface
     *
     * @param   \Magento\Event\Observer $observer
     * @return  \Magento\Weee\Model\Observer
     */
    public function addWeeeTaxAttributeType(\Magento\Event\Observer $observer)
    {
        // adminhtml_product_attribute_types

        $response = $observer->getEvent()->getResponse();
        $types = $response->getTypes();
        $types[] = array(
            'value' => 'weee',
            'label' => __('Fixed Product Tax'),
            'hide_fields' => array(
                'is_unique',
                'is_required',
                'frontend_class',
                'is_configurable',

                '_scope',
                '_default_value',
                '_front_fieldset',
            ),
            'disabled_types' => array(
                \Magento\Catalog\Model\Product\Type::TYPE_GROUPED,
            )
        );

        $response->setTypes($types);

        return $this;
    }

    /**
     * Automaticaly assign backend model to weee attributes
     *
     * @param   \Magento\Event\Observer $observer
     * @return  \Magento\Weee\Model\Observer
     */
    public function assignBackendModelToAttribute(\Magento\Event\Observer $observer)
    {
        $backendModel = \Magento\Weee\Model\Attribute\Backend\Weee\Tax::getBackendModelName();
        /** @var $object \Magento\Eav\Model\Entity\Attribute\AbstractAttribute */
        $object = $observer->getEvent()->getAttribute();
        if ($object->getFrontendInput() == 'weee') {
            $object->setBackendModel($backendModel);
            if (!$object->getApplyTo()) {
                $applyTo = array();
                foreach (\Magento\Catalog\Model\Product\Type::getOptions() as $option) {
                    if ($option['value'] == \Magento\Catalog\Model\Product\Type::TYPE_GROUPED) {
                        continue;
                    }
                    $applyTo[] = $option['value'];
                }
                $object->setApplyTo($applyTo);
            }
        }

        return $this;
    }

    /**
     * Add custom element type for attributes form
     *
     * @param   \Magento\Event\Observer $observer
     */
    public function updateElementTypes(\Magento\Event\Observer $observer)
    {
        $response = $observer->getEvent()->getResponse();
        $types    = $response->getTypes();
        $types['weee'] = 'Magento\Weee\Block\Element\Weee\Tax';
        $response->setTypes($types);
        return $this;
    }

    /**
     * Update WEEE amounts discount percents
     *
     * @param   \Magento\Event\Observer $observer
     * @return  \Magento\Weee\Model\Observer
     */
    public function updateDiscountPercents(\Magento\Event\Observer $observer)
    {
        if (!\Mage::helper('Magento\Weee\Helper\Data')->isEnabled()) {
            return $this;
        }

        $productCondition = $observer->getEvent()->getProductCondition();
        if ($productCondition) {
            $eventProduct = $productCondition;
        } else {
            $eventProduct = $observer->getEvent()->getProduct();
        }
        \Mage::getModel('Magento\Weee\Model\Tax')->updateProductsDiscountPercent($eventProduct);

        return $this;
    }

    /**
     * Update configurable options of the product view page
     *
     * @param   \Magento\Event\Observer $observer
     * @return  \Magento\Weee\Model\Observer
     */
    public function updateConfigurableProductOptions(\Magento\Event\Observer $observer)
    {
        /* @var $helper \Magento\Weee\Helper\Data */
        $helper = \Mage::helper('Magento\Weee\Helper\Data');
        if (!$helper->isEnabled()) {
            return $this;
        }

        $response = $observer->getEvent()->getResponseObject();
        $options  = $response->getAdditionalOptions();

        $_product = \Mage::registry('current_product');
        if (!$_product) {
            return $this;
        }

        $options['oldPlusDisposition'] = $helper->getOriginalAmount($_product);
        $options['plusDisposition'] = $helper->getAmount($_product);

        // Exclude Weee amount from excluding tax amount
        if (!$helper->typeOfDisplay($_product, array(
            \Magento\Weee\Model\Tax::DISPLAY_INCL, \Magento\Weee\Model\Tax::DISPLAY_INCL_DESCR,
        ))) {
            $options['exclDisposition'] = true;
        }

        $response->setAdditionalOptions($options);

        return $this;
    }

    /**
     * Process bundle options selection for prepare view json
     *
     * @param   \Magento\Event\Observer $observer
     * @return  \Magento\Weee\Model\Observer
     */
    public function updateBundleProductOptions(\Magento\Event\Observer $observer)
    {
        /* @var $weeeHelper \Magento\Weee\Helper\Data */
        $weeeHelper = \Mage::helper('Magento\Weee\Helper\Data');
        if (!$weeeHelper->isEnabled()) {
            return $this;
        }

        $response = $observer->getEvent()->getResponseObject();
        $selection = $observer->getEvent()->getSelection();
        $options = $response->getAdditionalOptions();

        $_product = \Mage::registry('current_product');

        $typeDynamic = \Magento\Bundle\Block\Adminhtml\Catalog\Product\Edit\Tab\Attributes\Extend::DYNAMIC;
        if (!$_product || $_product->getPriceType() != $typeDynamic) {
            return $this;
        }

        $amount          = $weeeHelper->getAmount($selection);
        $attributes      = $weeeHelper->getProductWeeeAttributes($_product, null, null, null, $weeeHelper->isTaxable());
        $amountInclTaxes = $weeeHelper->getAmountInclTaxes($attributes);
        $taxes           = $amountInclTaxes - $amount;
        $options['plusDisposition']    = $amount;
        $options['plusDispositionTax'] = ($taxes < 0) ? 0 : $taxes;
        // Exclude Weee amount from excluding tax amount
        if (!$weeeHelper->typeOfDisplay($_product, array(0, 1, 4))) {
            $options['exclDisposition'] = true;
        }

        $response->setAdditionalOptions($options);

        return $this;
    }
}
