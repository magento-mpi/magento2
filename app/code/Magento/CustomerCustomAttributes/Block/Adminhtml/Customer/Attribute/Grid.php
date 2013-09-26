<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_CustomerCustomAttributes
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Customer Attributes Grid Block
 */
class Magento_CustomerCustomAttributes_Block_Adminhtml_Customer_Attribute_Grid
    extends Magento_Eav_Block_Adminhtml_Attribute_Grid_Abstract
{
    /**
     * @var Magento_Customer_Model_Resource_Attribute_CollectionFactory
     */
    protected $_attributesFactory;

    /**
     * @param Magento_Core_Helper_Data $coreData
     * @param Magento_Backend_Block_Template_Context $context
     * @param Magento_Core_Model_StoreManagerInterface $storeManager
     * @param Magento_Core_Model_Url $urlModel
     * @param Magento_Customer_Model_Resource_Attribute_CollectionFactory $attributesFactory
     * @param array $data
     */
    public function __construct(
        Magento_Core_Helper_Data $coreData,
        Magento_Backend_Block_Template_Context $context,
        Magento_Core_Model_StoreManagerInterface $storeManager,
        Magento_Core_Model_Url $urlModel,
        Magento_Customer_Model_Resource_Attribute_CollectionFactory $attributesFactory,
        array $data = array()
    ) {
        $this->_attributesFactory = $attributesFactory;
        parent::__construct($coreData, $context, $storeManager, $urlModel, $data);
    }

    /**
     * Initialize grid, set grid Id
     *
     */
    protected function _construct()
    {
        parent::_construct();
        $this->setId('customerAttributeGrid');
        $this->setDefaultSort('sort_order');
    }

    /**
     * Prepare customer attributes grid collection object
     *
     * @return Magento_CustomerCustomAttributes_Block_Adminhtml_Customer_Attribute_Grid
     */
    protected function _prepareCollection()
    {
        /** @var $collection Magento_Customer_Model_Resource_Attribute_Collection */
        $collection = $this->_attributesFactory->create();
        $collection->addSystemHiddenFilter()->addExcludeHiddenFrontendFilter();
        $this->setCollection($collection);
        return parent::_prepareCollection();
    }

    /**
     * Prepare customer attributes grid columns
     *
     * @return Magento_CustomerCustomAttributes_Block_Adminhtml_Customer_Attribute_Grid
     */
    protected function _prepareColumns()
    {
        parent::_prepareColumns();

        $this->addColumn('is_visible', array(
            'header'    => __('Visible to Customer'),
            'sortable'  => true,
            'index'     => 'is_visible',
            'type'      => 'options',
            'options'   => array(
                '0' => __('No'),
                '1' => __('Yes'),
            ),
            'align'     => 'center',
        ));

        $this->addColumn('sort_order', array(
            'header'    => __('Sort Order'),
            'sortable'  => true,
            'align'     => 'center',
            'index'     => 'sort_order'
        ));

        return $this;
    }
}
