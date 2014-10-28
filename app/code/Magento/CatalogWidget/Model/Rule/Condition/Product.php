<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */


/**
 * CatalogWidget Rule Product Condition data model
 */
namespace Magento\CatalogWidget\Model\Rule\Condition;

/**
 * Class Product
 */
class Product extends \Magento\Rule\Model\Condition\Product\AbstractProduct
{
    /**
     * {@inheritdoc}
     */
    protected $elementName = 'parameters';

    /**
     * Load attribute options
     *
     * @return $this
     */
    public function loadAttributeOptions()
    {
        $productAttributes = $this->_productResource->loadAllAttributes()->getAttributesByCode();

        $attributes = array();
        foreach ($productAttributes as $attribute) {
            if (!$attribute->getFrontendLabel() || $attribute->getFrontendInput() == 'text') {
                continue;
            }
            $attributes[$attribute->getAttributeCode()] = $attribute->getFrontendLabel();
        }

        $this->_addSpecialAttributes($attributes);

        asort($attributes);
        $this->setAttributeOption($attributes);

        return $this;
    }
}
