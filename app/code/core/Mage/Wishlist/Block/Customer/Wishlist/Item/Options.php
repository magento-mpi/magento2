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
class Mage_Wishlist_Block_Customer_Wishlist_Item_Options extends Mage_Wishlist_Block_Abstract
{
    /*
     * List of product options rendering configurations by product type
     *
     * @var array
     */
    protected $_optionsCfg = array('default' => array(
        'helper' => 'Mage_Catalog_Helper_Product_Configuration',
        'template' => 'Mage_Wishlist::options_list.phtml'
    ));

    /**
     * Initialize block
     */
    public function __construct()
    {
        parent::__construct();
        Mage::dispatchEvent('product_option_renderer_init', array('block' => $this));
    }

    /*
     * Adds config for rendering product type options
     *
     * @param string $productType
     * @param string $helperName
     * @param null|string $template
     * @return Mage_Wishlist_Block_Customer_Wishlist_Item_Options
     */
    public function addOptionsRenderCfg($productType, $helperName, $template = null)
    {
        $this->_optionsCfg[$productType] = array('helper' => $helperName, 'template' => $template);
        return $this;
    }

    /**
     * Get item options renderer config
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
     * Render block html
     *
     * @return string
     */
    protected function _toHtml()
    {
        $cfg = $this->getOptionsRenderCfg($this->getItem()->getProduct()->getTypeId());
        if (!$cfg) {
            return '';
        }

        $helper = Mage::helper($cfg['helper']);
        if (!($helper instanceof Mage_Catalog_Helper_Product_Configuration_Interface)) {
            Mage::throwException($this->__("Helper for wishlist options rendering doesn't implement required interface."));
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

        $o = $helper->getOptions($this->getItem());
        $this->setTemplate($template)
            ->setOptionList($o);
        return parent::_toHtml();
    }
}
