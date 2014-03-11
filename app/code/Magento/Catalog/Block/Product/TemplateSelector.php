<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Catalog
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Catalog\Block\Product;

/**
 * Product gallery
 *
 * @SuppressWarnings(PHPMD.LongVariable)
 */
class TemplateSelector extends \Magento\View\Element\Template
{
    /**
     * Core registry
     *
     * @var \Magento\Registry
     */
    protected $_coreRegistry = null;

    /**
     * Set collection factory
     *
     * @var \Magento\Eav\Model\Resource\Entity\Attribute\Set\CollectionFactory
     */
    protected $_setColFactory;

    /**
     * Catalog resource helper
     *
     * @var \Magento\Catalog\Model\Resource\Helper
     */
    protected $_resourceHelper;

    /**
     * @param \Magento\View\Element\Template\Context $context
     * @param \Magento\Eav\Model\Resource\Entity\Attribute\Set\CollectionFactory $setColFactory
     * @param \Magento\Registry $registry
     * @param \Magento\Catalog\Model\Resource\Helper $resourceHelper
     * @param array $data
     */
    public function __construct(
        \Magento\View\Element\Template\Context $context,
        \Magento\Eav\Model\Resource\Entity\Attribute\Set\CollectionFactory $setColFactory,
        \Magento\Registry $registry,
        \Magento\Catalog\Model\Resource\Helper $resourceHelper,
        array $data = array()
    ) {
        $this->_setColFactory = $setColFactory;
        $this->_coreRegistry = $registry;
        $this->_resourceHelper = $resourceHelper;
        parent::__construct($context, $data);
    }

    /**
     * Retrieve list of product templates with search part contained in label
     *
     * @param string $labelPart
     * @return array
     */
    public function getSuggestedTemplates($labelPart)
    {
        $product = $this->_coreRegistry->registry('product');
        $entityType = $product->getResource()->getEntityType();
        $labelPart = $this->_resourceHelper->addLikeEscape($labelPart, array('position' => 'any'));
        /** @var \Magento\Eav\Model\Resource\Entity\Attribute\Set\Collection $collection */
        $collection = $this->_setColFactory->create();
        $collection->setEntityTypeFilter($entityType->getId())
            ->addFieldToFilter('attribute_set_name', array('like' => $labelPart))
            ->addFieldToSelect('attribute_set_id', 'id')
            ->addFieldToSelect('attribute_set_name', 'label')
            ->setOrder(
                'attribute_set_name',
                \Magento\Eav\Model\Resource\Entity\Attribute\Set\Collection::SORT_ORDER_ASC
        );
        return $collection->getData();
    }
}
