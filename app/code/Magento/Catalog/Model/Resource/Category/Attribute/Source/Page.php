<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Catalog
 * @copyright   {copyright}
 * @license     {license_link}
 */


/**
 * Catalog category landing page attribute source
 *
 * @category    Magento
 * @package     Magento_Catalog
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Magento_Catalog_Model_Resource_Category_Attribute_Source_Page
    extends Magento_Eav_Model_Entity_Attribute_Source_Abstract
{
    /**
     * Block collection factory
     *
     * @var Magento_Cms_Model_Resource_Block_CollectionFactory
     */
    protected $_blockCollectionFactory;

    /**
     * Construct
     *
     * @param Magento_Cms_Model_Resource_Block_CollectionFactory
     * $blockCollectionFactory
     */
    public function __construct(
        Magento_Cms_Model_Resource_Block_CollectionFactory $blockCollectionFactory
    ) {
        $this->_blockCollectionFactory = $blockCollectionFactory;
    }

    /**
     * Return all block options
     *
     * @return array
     */
    public function getAllOptions()
    {
        if (!$this->_options) {
            $this->_options = $this->_blockCollectionFactory->create()
                ->load()
                ->toOptionArray();
            array_unshift($this->_options, array('value' => '', 'label' => __('Please select a static block.')));
        }
        return $this->_options;
    }
}
