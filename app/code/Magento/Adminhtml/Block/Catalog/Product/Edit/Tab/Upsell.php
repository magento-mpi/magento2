<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Adminhtml
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Upsell products admin grid
 *
 * @category   Magento
 * @package    Magento_Adminhtml
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Magento_Adminhtml_Block_Catalog_Product_Edit_Tab_Upsell extends Magento_Adminhtml_Block_Widget_Grid
{
    /**
     * Core registry
     *
     * @var Magento_Core_Model_Registry
     */
    protected $_coreRegistry = null;

    /**
     * @var Magento_Catalog_Model_Product_LinkFactory
     */
    protected $_linkFactory;

    /**
     * @var Magento_Eav_Model_Resource_Entity_Attribute_Set_CollectionFactory]
     */
    protected $_setsFactory;

    /**
     * @var Magento_Catalog_Model_ProductFactory
     */
    protected $_productFactory;

    /**
     * @var Magento_Catalog_Model_Product_Type
     */
    protected $_type;

    /**
     * @var Magento_Catalog_Model_Product_Status
     */
    protected $_status;

    /**
     * @var Magento_Catalog_Model_Product_Visibility
     */
    protected $_visibility;

    /**
     * @param Magento_Catalog_Model_Product_LinkFactory $linkFactory
     * @param Magento_Eav_Model_Resource_Entity_Attribute_Set_CollectionFactory $setsFactory
     * @param Magento_Catalog_Model_ProductFactory $productFactory
     * @param Magento_Catalog_Model_Product_Type $type
     * @param Magento_Catalog_Model_Product_Status $status
     * @param Magento_Catalog_Model_Product_Visibility $visibility
     * @param Magento_Core_Helper_Data $coreData
     * @param Magento_Backend_Block_Template_Context $context
     * @param Magento_Core_Model_StoreManagerInterface $storeManager
     * @param Magento_Core_Model_Url $urlModel
     * @param Magento_Core_Model_Registry $coreRegistry
     * @param array $data
     *
     * @SuppressWarnings(PHPMD.ExcessiveParameterList)
     */
    public function __construct(
        Magento_Catalog_Model_Product_LinkFactory $linkFactory,
        Magento_Eav_Model_Resource_Entity_Attribute_Set_CollectionFactory $setsFactory,
        Magento_Catalog_Model_ProductFactory $productFactory,
        Magento_Catalog_Model_Product_Type $type,
        Magento_Catalog_Model_Product_Status $status,
        Magento_Catalog_Model_Product_Visibility $visibility,
        Magento_Core_Helper_Data $coreData,
        Magento_Backend_Block_Template_Context $context,
        Magento_Core_Model_StoreManagerInterface $storeManager,
        Magento_Core_Model_Url $urlModel,
        Magento_Core_Model_Registry $coreRegistry,
        array $data = array()
    ) {
        $this->_linkFactory = $linkFactory;
        $this->_setsFactory = $setsFactory;
        $this->_productFactory = $productFactory;
        $this->_type = $type;
        $this->_status = $status;
        $this->_visibility = $visibility;
        $this->_coreRegistry = $coreRegistry;
        parent::__construct($coreData, $context, $storeManager, $urlModel, $data);
    }

    /**
     * Set grid params
     *
     */
    protected function _construct()
    {
        parent::_construct();
        $this->setId('up_sell_product_grid');
        $this->setDefaultSort('entity_id');
        $this->setUseAjax(true);
        if ($this->getProduct() && $this->getProduct()->getId()) {
            $this->setDefaultFilter(array('in_products'=>1));
        }
        if ($this->isReadonly()) {
            $this->setFilterVisibility(false);
        }
    }

    /**
     * Retirve currently edited product model
     *
     * @return Magento_Catalog_Model_Product
     */
    public function getProduct()
    {
        return $this->_coreRegistry->registry('current_product');
    }

    /**
     * Add filter
     *
     * @param object $column
     * @return Magento_Adminhtml_Block_Catalog_Product_Edit_Tab_Upsell
     */
    protected function _addColumnFilterToCollection($column)
    {
        // Set custom filter for in product flag
        if ($column->getId() == 'in_products') {
            $productIds = $this->_getSelectedProducts();
            if (empty($productIds)) {
                $productIds = 0;
            }
            if ($column->getFilter()->getValue()) {
                $this->getCollection()->addFieldToFilter('entity_id', array('in'=>$productIds));
            } else {
                if($productIds) {
                    $this->getCollection()->addFieldToFilter('entity_id', array('nin'=>$productIds));
                }
            }
        } else {
            parent::_addColumnFilterToCollection($column);
        }
        return $this;
    }

    /**
     * Checks when this block is readonly
     *
     * @return boolean
     */
    public function isReadonly()
    {
        return $this->getProduct() && $this->getProduct()->getUpsellReadonly();
    }

    /**
     * Prepare collection
     *
     * @return Magento_Adminhtml_Block_Widget_Grid
     */
    protected function _prepareCollection()
    {
        $collection = $this->_linkFactory->create()->useUpSellLinks()
            ->getProductCollection()
            ->setProduct($this->getProduct())
            ->addAttributeToSelect('*');

        if ($this->isReadonly()) {
            $productIds = $this->_getSelectedProducts();
            if (empty($productIds)) {
                $productIds = array(0);
            }
            $collection->addFieldToFilter('entity_id', array('in'=>$productIds));
        }

        $this->setCollection($collection);
        return parent::_prepareCollection();
    }

    /**
     * Add columns to grid
     *
     * @return Magento_Adminhtml_Block_Widget_Grid
     */
    protected function _prepareColumns()
    {
        if (!$this->getProduct()->getUpsellReadonly()) {
            $this->addColumn('in_products', array(
                'type'      => 'checkbox',
                'name'      => 'in_products',
                'values'    => $this->_getSelectedProducts(),
                'align'     => 'center',
                'index'     => 'entity_id',
                'header_css_class'  => 'col-select',
                'column_css_class'  => 'col-select'
            ));
        }

        $this->addColumn('entity_id', array(
            'header'    => __('ID'),
            'sortable'  => true,
            'index'     => 'entity_id',
            'header_css_class'  => 'col-id',
            'column_css_class'  => 'col-id'
        ));
        $this->addColumn('name', array(
            'header'    => __('Name'),
            'index'     => 'name',
            'header_css_class'  => 'col-name',
            'column_css_class'  => 'col-name'
        ));

        $this->addColumn('type', array(
            'header'    => __('Type'),
            'index'     => 'type_id',
            'type'      => 'options',
            'options'   => $this->_type->getOptionArray(),
            'header_css_class'  => 'col-type',
            'column_css_class'  => 'col-type'
        ));

        $sets = $this->_setsFactory->create()
            ->setEntityTypeFilter($this->_productFactory->create()->getResource()->getTypeId())
            ->load()
            ->toOptionHash();

        $this->addColumn('set_name', array(
            'header'    => __('Attribute Set'),
            'index'     => 'attribute_set_id',
            'type'      => 'options',
            'options'   => $sets,
            'header_css_class'  => 'col-attr-name',
            'column_css_class'  => 'col-attr-name'
        ));

        $this->addColumn('status', array(
            'header'    => __('Status'),
            'index'     => 'status',
            'type'      => 'options',
            'options'   => $this->_status->getOptionArray(),
            'header_css_class'  => 'col-status',
            'column_css_class'  => 'col-status'
        ));

        $this->addColumn('visibility', array(
            'header'    => __('Visibility'),
            'index'     => 'visibility',
            'type'      => 'options',
            'options'   => $this->_visibility->getOptionArray(),
            'header_css_class'  => 'col-visibility',
            'column_css_class'  => 'col-visibility'
        ));

        $this->addColumn('sku', array(
            'header'    => __('SKU'),
            'index'     => 'sku',
            'header_css_class'  => 'col-sku',
            'column_css_class'  => 'col-sku'
        ));

        $this->addColumn('price', array(
            'header'        => __('Price'),
            'type'          => 'currency',
            'currency_code' => (string) $this->_storeConfig->getConfig(Magento_Directory_Model_Currency::XML_PATH_CURRENCY_BASE),
            'index'         => 'price',
            'header_css_class'  => 'col-price',
            'column_css_class'  => 'col-price'
        ));

        $this->addColumn('position', array(
            'header'            => __('Position'),
            'name'              => 'position',
            'type'              => 'number',
            'validate_class'    => 'validate-number',
            'index'             => 'position',
            'editable'          => !$this->getProduct()->getUpsellReadonly(),
            'edit_only'         => !$this->getProduct()->getId(),
            'header_css_class'  => 'col-position',
            'column_css_class'  => 'col-position'
        ));

        return parent::_prepareColumns();
    }

    /**
     * Rerieve grid URL
     *
     * @return string
     */
    public function getGridUrl()
    {
        return $this->_getData('grid_url') ? $this->_getData('grid_url') : $this->getUrl('*/*/upsellGrid', array('_current'=>true));
    }

    /**
     * Retrieve selected upsell products
     *
     * @return array
     */
    protected function _getSelectedProducts()
    {
        $products = $this->getProductsUpsell();
        if (!is_array($products)) {
            $products = array_keys($this->getSelectedUpsellProducts());
        }
        return $products;
    }

    /**
     * Retrieve upsell products
     *
     * @return array
     */
    public function getSelectedUpsellProducts()
    {
        $products = array();
        foreach ($this->_coreRegistry->registry('current_product')->getUpSellProducts() as $product) {
            $products[$product->getId()] = array('position' => $product->getPosition());
        }
        return $products;
    }

}
