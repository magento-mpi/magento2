<?php
/**
 * {license_notice}
 *
 * @category    Mage
 * @package     Mage_Launcher
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Product Drawer Block
 *
 * @category   Mage
 * @package    Mage_Launcher
 * @author     Magento Core Team <core@magentocommerce.com>
 */
class Mage_Launcher_Block_Adminhtml_Storelauncher_Product_Drawer extends Mage_Launcher_Block_Adminhtml_Drawer
{
     /**
      * Product Model
      *
      * @var Mage_Catalog_Model_Product
      */
     protected $_productModel;

     /**
      * Inline Translate Model
      *
      * @var Mage_Core_Model_Translate_Inline
      */
     protected $_translateInline;

     /**
      * EAV Entity Model
      *
      * @var Mage_Eav_Model_Entity
      */
     protected $_entityModel;

     /**
      * Entity Attribute Set model
      *
      * @var Mage_Eav_Model_Entity_Attribute_Set
      */
     protected  $_entityAttrSet;

     /**
      * Entity Attribute Set Group Collection
      *
      * @var Mage_Eav_Model_Resource_Entity_Attribute_Group_Collection
      */
     protected $_attrSetGroupColl;

     /**
      * Minimal Attribute Set ID
      *
      * @var int
      */
     protected $_minimalAttrSetId;

    /**
     * @param Mage_Core_Controller_Request_Http $request
     * @param Mage_Core_Model_Layout $layout
     * @param Mage_Core_Model_Event_Manager $eventManager
     * @param Mage_Backend_Model_Url $urlBuilder
     * @param Mage_Core_Model_Translate $translator
     * @param Mage_Core_Model_Cache $cache
     * @param Mage_Core_Model_Design_Package $designPackage
     * @param Mage_Core_Model_Session $session
     * @param Mage_Core_Model_Store_Config $storeConfig
     * @param Mage_Core_Controller_Varien_Front $frontController
     * @param Mage_Core_Model_Factory_Helper $helperFactory
     * @param Mage_Core_Model_Dir $dirs,
     * @param Mage_Core_Model_Logger $logger,
     * @param Magento_Filesystem $filesystem,
     * @param Mage_Launcher_Model_LinkTracker $linkTracker
     * @param Mage_Catalog_Model_Product $productModel,
     * @param Mage_Core_Model_Translate_Inline $translateInline,
     * @param Mage_Eav_Model_Entity $entityModel,
     * @param Mage_Eav_Model_Entity_Attribute_Set $entityAttrSet,
     * @param Mage_Eav_Model_Resource_Entity_Attribute_Group_Collection $attrSetGroup,
     * @param array $data
     *
     * @SuppressWarnings(PHPMD.ExcessiveParameterList)
     */
    public function __construct(
        Mage_Core_Controller_Request_Http $request,
        Mage_Core_Model_Layout $layout,
        Mage_Core_Model_Event_Manager $eventManager,
        Mage_Backend_Model_Url $urlBuilder,
        Mage_Core_Model_Translate $translator,
        Mage_Core_Model_Cache $cache,
        Mage_Core_Model_Design_Package $designPackage,
        Mage_Core_Model_Session $session,
        Mage_Core_Model_Store_Config $storeConfig,
        Mage_Core_Controller_Varien_Front $frontController,
        Mage_Core_Model_Factory_Helper $helperFactory,
        Mage_Core_Model_Dir $dirs,
        Mage_Core_Model_Logger $logger,
        Magento_Filesystem $filesystem,
        Mage_Launcher_Model_LinkTracker $linkTracker,
        Mage_Catalog_Model_Product $productModel,
        Mage_Core_Model_Translate_Inline $translateInline,
        Mage_Eav_Model_Entity $entityModel,
        Mage_Eav_Model_Entity_Attribute_Set $entityAttrSet,
        Mage_Eav_Model_Resource_Entity_Attribute_Group_Collection $attrSetGroupColl,
        array $data = array()
    ) {
        parent::__construct($request, $layout, $eventManager, $urlBuilder, $translator, $cache, $designPackage,
            $session, $storeConfig, $frontController, $helperFactory, $dirs, $logger, $filesystem, $linkTracker, $data
        );
        $this->_productModel = $productModel;
        $this->_translateInline = $translateInline;
        $this->_entityModel = $entityModel;
        $this->_entityAttrSet = $entityAttrSet;
        $this->_attrSetGroupColl = $attrSetGroupColl;

        $this->_minimalAttrSetId = $this->_getMinimalAttrSetId();
        $this->_initProduct(Mage_Catalog_Model_Product_Type::TYPE_SIMPLE, $this->_minimalAttrSetId);
    }

    /**
     * Get Translated Tile Header
     *
     * @return string
     */
    public function getTileHeader()
    {
        return $this->helper('Mage_Launcher_Helper_Data')->__('Add Product');
    }

    /**
     * Generate HTML content of Create Product 'General Tab'
     *
     * @return string
     */
    public function getGeneralTabContent()
    {
        Mage::dispatchEvent('catalog_product_new_action', array('product' => $this->_productModel));

        $group = $this->_attrSetGroupColl
            ->setAttributeSetFilter($this->_minimalAttrSetId)
            ->setSortOrder()
            ->fetchItem();

        $attributes = $this->_productModel->getAttributes($group->getId(), true);

        foreach ($attributes as $key => $attribute) {
            if (!$attribute->getIsVisible()) {
                unset($attributes[$key]);
            }
        }

        Mage::register('use_wrapper', false);
        $tabAttributesBlock = $this->getLayout()->createBlock(
            'Mage_Adminhtml_Block_Catalog_Product_Edit_Tab_Attributes',
            'product_tabs_attributes_tab'
        );
        $content = $this->_translateHtml(
            $tabAttributesBlock->setGroup($group)->setGroupAttributes($attributes)->toHtml()
        );

        return $content;
    }

    /**
     * Initialize product
     *
     * @param string $typeId
     * @param int $setId
     * @return Mage_Catalog_Model_Product
     * @TODO This function should be placed in ProductFactory Model or Product Model
     */
    protected function _initProduct($typeId, $setId)
    {
        $this->_productModel->setStoreId(null);

        $this->_productModel->setTypeId($typeId);
        $this->_productModel->setData('_edit_mode', true);
        $this->_productModel->setAttributeSetId($setId);

        Mage::register('product', $this->_productModel);
        Mage::register('current_product', $this->_productModel);
        return $this->_productModel;
    }

    /**
     * Translate html content
     *
     * @param string $html
     * @return string
     */
    protected function _translateHtml($html)
    {
        $this->_translateInline->processResponseBody($html);
        return $html;
    }

    /**
     * Get Minimal Attribute Set Id
     *
     * @return int
     */
    protected function _getMinimalAttrSetId()
    {
        $entityTypeId = $this->_entityModel
            ->setType(Mage_Catalog_Model_Product::ENTITY)
            ->getTypeId();

        return $this->_entityAttrSet->getMinimalAttrSetId($entityTypeId);
    }
}
