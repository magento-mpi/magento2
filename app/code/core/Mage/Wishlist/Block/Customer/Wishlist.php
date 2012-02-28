<?php
/**
 * {license_notice}
 *
 * @category    Mage
 * @package     Mage_Wishlist
 * @copyright   {copyright}
 * @license     {license_link}
 */


/**
 * Wishlist block customer items
 *
 * @category   Mage
 * @package    Mage_Wishlist
 * @author     Magento Core Team <core@magentocommerce.com>
 */
class Mage_Wishlist_Block_Customer_Wishlist extends Mage_Wishlist_Block_Abstract
{
    /*
     * List of product type configuration to render options list
     */
    protected $_optionsCfg = array();

    /*
     * Constructor of block - adds default renderer for product configuration
     */
    public function _construct()
    {
        parent::_construct();
        $this->addOptionsRenderCfg('default','Mage_Catalog_Helper_Product_Configuration','options_list.phtml');
    }

    /**
     * Add wishlist conditions to collection
     *
     * @param  Mage_Wishlist_Model_Resource_Item_Collection $collection
     * @return Mage_Wishlist_Block_Customer_Wishlist
     */
    protected function _prepareCollection($collection)
    {
        $collection->setInStockFilter(true)->setOrder('added_at', 'ASC');
        return $this;
    }

    /**
     * Preparing global layout
     *
     * @return Mage_Wishlist_Block_Customer_Wishlist
     */
    protected function _prepareLayout()
    {
        parent::_prepareLayout();
        $headBlock = $this->getLayout()->getBlock('head');
        if ($headBlock) {
            $headBlock->setTitle($this->__('My Wishlist'));
        }
    }

    /**
     * Retrieve Back URL
     *
     * @return string
     */
    public function getBackUrl()
    {
        if ($this->getRefererUrl()) {
            return $this->getRefererUrl();
        }
        return $this->getUrl('customer/account/');
    }

    /**
     * Sets all options render configurations
     *
     * @param null|array $optionCfg
     * @return Mage_Wishlist_Block_Customer_Wishlist
     */
    public function setOptionsRenderCfgs($optionCfg)
    {
        $this->_optionsCfg = $optionCfg;
        return $this;
    }

    /**
     * Returns all options render configurations
     *
     * @return array
     */
    public function getOptionsRenderCfgs()
    {
        return $this->_optionsCfg;
    }

    /*
     * Adds config for rendering product type options
     * If template is null - later default will be used
     *
     * @param string $productType
     * @param string $helperName
     * @param null|string $template
     * @return Mage_Wishlist_Block_Customer_Wishlist
     */
    public function addOptionsRenderCfg($productType, $helperName, $template = null)
    {
        $this->_optionsCfg[$productType] = array('helper' => $helperName, 'template' => $template);
        return $this;
    }

    /**
     * Returns html for showing item options
     *
     * @param string $productType
     * @return array|null
     */
    public function getOptionsRenderCfg($productType)
    {
        if (isset($this->_optionsCfg[$productType])) {
            return $this->_optionsCfg[$productType];
        } elseif (isset($this->_optionsCfg['default'])) {
            return $this->_optionsCfg['default'];
        } else {
            return null;
        }
    }

    /**
     * Returns html for showing item options
     *
     * @param Mage_Wishlist_Model_Item $item
     * @return string
     */
    public function getDetailsHtml(Mage_Wishlist_Model_Item $item)
    {
        $cfg = $this->getOptionsRenderCfg($item->getProduct()->getTypeId());
        if (!$cfg) {
            return '';
        }

        $helper = Mage::helper($cfg['helper']);
        if (!($helper instanceof Mage_Catalog_Helper_Product_Configuration_Interface)) {
            Mage::throwException($this->__("Helper for wishlist options rendering doesn't implement required interface."));
        }

        $block = $this->getChildBlock('item_options');
        if (!$block) {
            return '';
        }

        if ($cfg['template']) {
            $template = $cfg['template'];
        } else {
            $cfgDefault = $this->getOptionsRenderCfg('default');
            if (!$cfgDefault) {
                return '';
            }
            $template = $cfgDefault['template'];
        }

        return $block->setTemplate($template)
            ->setOptionList($helper->getOptions($item))
            ->toHtml();
    }

    /**
     * Returns default description to show in textarea field
     *
     * @param Mage_Wishlist_Model_Item $item
     * @return string
     */
    public function getCommentValue(Mage_Wishlist_Model_Item $item)
    {
        if ($this->hasDescription($item)) {
            return  $this->getEscapedDescription($item);
        }

        return Mage::helper('Mage_Wishlist_Helper_Data')->defaultCommentString();
    }

    /**
     * Returns qty to show visually to user
     *
     * @param Mage_Wishlist_Model_Item $item
     * @return float
     */
    public function getAddToCartQty(Mage_Wishlist_Model_Item $item)
    {
        $qty = $this->getQty($item);
        return $qty ? $qty : 1;
    }
}
