<?php
/**
 * {license_notice}
 *
 * @category    Mage
 * @package     Mage_Catalog
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Block representing set of columns in product grid
 *
 * @category   Mage
 * @package    Mage_Catalog
 * @author     Magento Core Team <core@magentocommerce.com>
 *
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 */
class Mage_Catalog_Block_Product_Configurable_AssociatedSelector_Backend_Grid_ColumnSet
    extends Mage_Backend_Block_Widget_Grid_ColumnSet
{
    /**
     * Registry instance
     *
     * @var Mage_Core_Model_Registry
     */
    protected $_registryManager;

    /**
     * Product type configurable instance
     *
     * @var Mage_Catalog_Model_Product_Type_Configurable
     */
    protected $_productType;

    /**
     * @param Mage_Core_Controller_Request_Http $request
     * @param Mage_Core_Model_Layout $layout
     * @param Mage_Core_Model_Event_Manager $eventManager
     * @param Mage_Core_Model_Url $urlBuilder
     * @param Mage_Core_Model_Translate $translator
     * @param Mage_Core_Model_Cache $cache
     * @param Mage_Core_Model_Design_Package $designPackage
     * @param Mage_Core_Model_Session $session
     * @param Mage_Core_Model_Store_Config $storeConfig
     * @param Mage_Core_Controller_Varien_Front $frontController
     * @param Mage_Core_Model_Factory_Helper $helperFactory
     * @param Magento_Filesystem $filesystem
     * @param Mage_Backend_Model_Widget_Grid_Row_UrlGeneratorFactory $generatorFactory
     * @param Mage_Core_Model_Registry $registryManager,
     * @param Mage_Catalog_Model_Product_Type_Configurable $productType
     * @param Mage_Backend_Model_Widget_Grid_SubTotals $subtotals
     * @param Mage_Backend_Model_Widget_Grid_Totals $totals
     * @param array $data
     *
     * @SuppressWarnings(PHPMD.ExcessiveParameterList)
     */
    public function __construct(
        Mage_Core_Controller_Request_Http $request,
        Mage_Core_Model_Layout $layout,
        Mage_Core_Model_Event_Manager $eventManager,
        Mage_Core_Model_Url $urlBuilder,
        Mage_Core_Model_Translate $translator,
        Mage_Core_Model_Cache $cache,
        Mage_Core_Model_Design_Package $designPackage,
        Mage_Core_Model_Session $session,
        Mage_Core_Model_Store_Config $storeConfig,
        Mage_Core_Controller_Varien_Front $frontController,
        Mage_Core_Model_Factory_Helper $helperFactory,
        Magento_Filesystem $filesystem,
        Mage_Backend_Model_Widget_Grid_Row_UrlGeneratorFactory $generatorFactory,
        Mage_Core_Model_Registry $registryManager,
        Mage_Backend_Model_Widget_Grid_SubTotals $subtotals,
        Mage_Backend_Model_Widget_Grid_Totals $totals,
        Mage_Catalog_Model_Product_Type_Configurable $productType,
        array $data = array()
    ) {
        parent::__construct($request, $layout, $eventManager, $urlBuilder, $translator, $cache, $designPackage,
            $session, $storeConfig, $frontController, $helperFactory, $filesystem,
            $helperFactory->get('Mage_Backend_Helper_Data'), $generatorFactory, $subtotals, $totals, $data);

        $this->_registryManager = $registryManager;
        $this->_productType = $productType;
    }

    /**
     * Retrieve currently edited product object
     *
     * @return Mage_Catalog_Model_Product
     */
    protected function _getProduct()
    {
        return $this->_registryManager->registry('current_product');
    }

    /**
     * Preparing layout
     *
     * @return Mage_Catalog_Block_Product_Configurable_AssociatedSelector_Backend_Grid_ColumnSet
     */
    protected function _prepareLayout()
    {
        parent::_prepareLayout();

        $product = $this->_getProduct();
        $attributes = $this->_productType->getUsedProductAttributes($product);
        /** @var $attribute Mage_Catalog_Model_Entity_Attribute */
        foreach ($attributes as $attribute) {
            /** @var $block Mage_Backend_Block_Widget_Grid_Column */
            $block = $this->addChild(
                $attribute->getAttributeCode(),
                'Mage_Backend_Block_Widget_Grid_Column',
                array(
                    'header' => $attribute->getStoreLabel(),
                    'index' => $attribute->getAttributeCode(),
                    'type' => 'options',
                    'options' => $this->getOptions($attribute->getSource()),
                    'sortable' => false
                )
            );
            $block->setId($attribute->getAttributeCode())->setGrid($this);
        }
        return $this;
    }

    /**
     * Get option as hash
     *
     * @param Mage_Eav_Model_Entity_Attribute_Source_Abstract $sourceModel
     * @return array
     */
    private function getOptions(Mage_Eav_Model_Entity_Attribute_Source_Abstract $sourceModel)
    {
        $result = array();
        foreach ($sourceModel->getAllOptions() as $option) {
            if ($option['value'] != '') {
                $result[$option['value']] = $option['label'];
            }
        }
        return $result;
    }
}
