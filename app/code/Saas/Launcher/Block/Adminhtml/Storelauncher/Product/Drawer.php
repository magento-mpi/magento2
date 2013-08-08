<?php
/**
 * {license_notice}
 *
 * @category    Mage
 * @package     Saas_Launcher
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Product Drawer Block
 *
 * @category   Mage
 * @package    Saas_Launcher
 * @author     Magento Core Team <core@magentocommerce.com>
 *
 * @SuppressWarnings(PHPMD.DepthOfInheritance)
 */
class Saas_Launcher_Block_Adminhtml_Storelauncher_Product_Drawer extends Saas_Launcher_Block_Adminhtml_Drawer
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
      * Entity Attribute Set Group Collection
      *
      * @var Mage_Eav_Model_Resource_Entity_Attribute_Group_Collection
      */
     protected $_attrSetGroupColl;

     /**
      * Default Attribute Set ID
      *
      * @var int
      */
     protected $_defaultAttrSetId;

    /**
     * @param Mage_Backend_Block_Template_Context $context
     * @param Saas_Launcher_Model_LinkTracker $linkTracker
     * @param Mage_Catalog_Model_Product $productModel
     * @param Mage_Core_Model_Translate_Inline $translateInline
     * @param Mage_Eav_Model_Resource_Entity_Attribute_Group_Collection $attrSetGroupColl
     * @param array $data
     */
    public function __construct(
        Mage_Backend_Block_Template_Context $context,
        Saas_Launcher_Model_LinkTracker $linkTracker,
        Mage_Catalog_Model_Product $productModel,
        Mage_Core_Model_Translate_Inline $translateInline,
        Mage_Eav_Model_Resource_Entity_Attribute_Group_Collection $attrSetGroupColl,
        array $data = array()
    ) {
        parent::__construct($context, $linkTracker, $data);
        $this->_productModel = $productModel;
        $this->_translateInline = $translateInline;
        $this->_attrSetGroupColl = $attrSetGroupColl;

        $this->_initProduct(Mage_Catalog_Model_Product_Type::TYPE_SIMPLE);
    }

    /**
     * Get Translated Tile Header
     *
     * @return string
     */
    public function getTileHeader()
    {
        return __('Product');
    }

    /**
     * Generate HTML content of Create Product 'General Tab'
     *
     * @return string
     */
    public function getGeneralTabContent()
    {
        Mage::dispatchEvent('catalog_product_new_action', array('product' => $this->_productModel));

        $this->_attrSetGroupColl
            ->setAttributeSetFilter($this->_defaultAttrSetId)
            ->addFieldToFilter('attribute_group_code', array('in' => array(
                Mage_Eav_Model_Resource_Entity_Attribute_Group::TAB_GENERAL_CODE,
                Mage_Eav_Model_Resource_Entity_Attribute_Group::TAB_IMAGE_MANAGEMENT_CODE
            )))
            ->setSortOrder()
            ->load();

        Mage::register('use_wrapper', false);
        $tabAttributesBlock = $this->getLayout()->createBlock(
            'Mage_Adminhtml_Block_Catalog_Product_Edit_Tab_Attributes',
            'product_tabs_attributes_tab'
        );
        $htmlContent = '';
        foreach ($this->_attrSetGroupColl as $group) {
            $attributes = $this->_productModel->getAttributes($group->getId(), true);

            foreach ($attributes as $key => $attribute) {
                if (!$attribute->getIsVisible()) {
                    unset($attributes[$key]);
                }
            }

            if ($attributes) {
                $htmlContent .= $this->_translateHtml(
                    $tabAttributesBlock->setGroup($group)->setGroupAttributes($attributes)->toHtml()
                );
            }
        }
        return $htmlContent;
    }

    /**
     * Initialize product
     *
     * @param string $typeId
     * @return Mage_Catalog_Model_Product
     * @TODO This function should be placed in ProductFactory Model or Product Model
     */
    protected function _initProduct($typeId)
    {
        $this->_productModel->setStoreId(null);

        $this->_productModel->setTypeId($typeId);
        $this->_productModel->setData('_edit_mode', true);
        $this->_defaultAttrSetId = $this->_productModel->getDefaultAttributeSetId();
        $this->_productModel->setAttributeSetId($this->_defaultAttrSetId);

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
}
