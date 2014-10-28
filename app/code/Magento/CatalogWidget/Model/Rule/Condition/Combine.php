<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * CatalogWidget Rule Combine Condition data model
 */
namespace Magento\CatalogWidget\Model\Rule\Condition;

class Combine extends \Magento\Rule\Model\Condition\Combine
{
    /**
     * @var \Magento\CatalogWidget\Model\Rule\Condition\ProductFactory
     */
    protected $productFactory;

    /**
     * {@inheritdoc}
     */
    protected $elementName = 'parameters';

    /**
     * @param \Magento\Rule\Model\Condition\Context $context
     * @param \Magento\CatalogWidget\Model\Rule\Condition\ProductFactory $conditionFactory
     * @param array $data
     */
    public function __construct(
        \Magento\Rule\Model\Condition\Context $context,
        \Magento\CatalogWidget\Model\Rule\Condition\ProductFactory $conditionFactory,
        array $data = array()
    ) {
        $this->productFactory = $conditionFactory;
        parent::__construct($context, $data);
        $this->setType('Magento\CatalogWidget\Model\Rule\Condition\Combine');
    }

    /**
     * @return array
     */
    public function getNewChildSelectOptions()
    {
        $productAttributes = $this->productFactory->create()->loadAttributeOptions()->getAttributeOption();
        $attributes = array();
        foreach ($productAttributes as $code => $label) {
            $attributes[] = array(
                'value' => 'Magento\CatalogWidget\Model\Rule\Condition\Product|' . $code,
                'label' => $label
            );
        }
        $conditions = parent::getNewChildSelectOptions();
        $conditions = array_merge_recursive(
            $conditions,
            array(
                array(
                    'value' => 'Magento\CatalogWidget\Model\Rule\Condition\Combine',
                    'label' => __('Conditions Combination')
                ),
                array('label' => __('Product Attribute'), 'value' => $attributes)
            )
        );
        return $conditions;
    }
}
