<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Backend
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Config category source
 *
 * @SuppressWarnings(PHPMD.LongVariable)
 */
class Magento_Catalog_Model_Config_Source_Category implements Magento_Core_Model_Option_ArrayInterface
{
    /**
     * Category collection factory
     *
     * @var Magento_Catalog_Model_Resource_Category_CollectionFactory
     */
    protected $_categoryCollectionFactory;

    /**
     * Construct
     *
     * @param Magento_Catalog_Model_Resource_Category_CollectionFactory $categoryCollectionFactory
     */
    public function __construct(
        Magento_Catalog_Model_Resource_Category_CollectionFactory $categoryCollectionFactory
    ) {
        $this->_categoryCollectionFactory = $categoryCollectionFactory;
    }

    public function toOptionArray($addEmpty = true)
    {
        /** @var Magento_Catalog_Model_Resource_Category_Collection $collection */
        $collection = $this->_categoryCollectionFactory->create();

        $collection->addAttributeToSelect('name')
            ->addRootLevelFilter()
            ->load();

        $options = array();

        if ($addEmpty) {
            $options[] = array(
                'label' => __('-- Please Select a Category --'),
                'value' => ''
            );
        }
        foreach ($collection as $category) {
            $options[] = array(
               'label' => $category->getName(),
               'value' => $category->getId()
            );
        }

        return $options;
    }
}
