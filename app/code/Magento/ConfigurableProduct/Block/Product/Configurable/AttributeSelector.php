<?php
/**
 * Select attributes suitable for product variations generation
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\ConfigurableProduct\Block\Product\Configurable;

/**
 * @SuppressWarnings(PHPMD.LongVariable)
 */
class AttributeSelector extends \Magento\Backend\Block\Template
{
    /**
     * Attribute collection factory
     *
     * @var \Magento\Catalog\Model\Resource\Product\Attribute\CollectionFactory
     */
    protected $_attributeColFactory;

    /**
     * Catalog resource helper
     *
     * @var \Magento\Catalog\Model\Resource\Helper
     */
    protected $_resourceHelper;

    /**
     * @param \Magento\Backend\Block\Template\Context $context
     * @param \Magento\Catalog\Model\Resource\Product\Attribute\CollectionFactory $attributeColFactory
     * @param \Magento\Catalog\Model\Resource\Helper $resourceHelper
     * @param array $data
     */
    public function __construct(
        \Magento\Backend\Block\Template\Context $context,
        \Magento\Catalog\Model\Resource\Product\Attribute\CollectionFactory $attributeColFactory,
        \Magento\Catalog\Model\Resource\Helper $resourceHelper,
        array $data = array()
    ) {
        $this->_attributeColFactory = $attributeColFactory;
        $this->_resourceHelper = $resourceHelper;
        parent::__construct($context, $data);
    }

    /**
     * Retrieve list of attributes with admin store label containing $labelPart
     *
     * @param string $labelPart
     * @return \Magento\Catalog\Model\Resource\Product\Attribute\Collection
     */
    public function getSuggestedAttributes($labelPart)
    {
        $escapedLabelPart = $this->_resourceHelper->addLikeEscape($labelPart, array('position' => 'any'));
        /** @var $collection \Magento\Catalog\Model\Resource\Product\Attribute\Collection */
        $collection = $this->_attributeColFactory->create();
        $collection->addFieldToFilter('frontend_input', 'select')
            ->addFieldToFilter('frontend_label', array('like' => $escapedLabelPart))
            ->addFieldToFilter('is_configurable', 1)
            ->addFieldToFilter('is_user_defined', 1)
            ->addFieldToFilter('is_global', \Magento\Catalog\Model\Resource\Eav\Attribute::SCOPE_GLOBAL);

        $result = array();
        $types = array(
            \Magento\Catalog\Model\Product\Type::TYPE_SIMPLE,
            \Magento\Catalog\Model\Product\Type::TYPE_VIRTUAL,
            \Magento\Catalog\Model\Product\Type::TYPE_CONFIGURABLE,
        );
        foreach ($collection->getItems() as $id => $attribute) {
            /** @var $attribute \Magento\Catalog\Model\Resource\Eav\Attribute */
            if (!$attribute->getApplyTo() || count(array_diff($types, $attribute->getApplyTo())) === 0) {
                $result[$id] = array(
                    'id'      => $attribute->getId(),
                    'label'   => $attribute->getFrontendLabel(),
                    'code'    => $attribute->getAttributeCode(),
                    'options' => $attribute->getSource()->getAllOptions(false)
                );
            }
        }
        return $result;
    }

    /**
     * Attribute set creation action URL
     *
     * @return string
     */
    public function getAttributeSetCreationUrl()
    {
        return $this->getUrl('*/product_set/save');
    }

    /**
     * Get options for suggest widget
     *
     * @return array
     */
    public function getSuggestWidgetOptions()
    {
        return array(
            'source' => $this->getUrl('*/product_attribute/suggestConfigurableAttributes'),
            'minLength' => 0,
            'className' => 'category-select',
            'showAll' => true,
        );
    }
}
