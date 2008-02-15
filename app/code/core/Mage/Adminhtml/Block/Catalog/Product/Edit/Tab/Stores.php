<?php
/**
 * Magento
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/osl-3.0.php
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@magentocommerce.com so we can send you a copy immediately.
 *
 * @category   Mage
 * @package    Mage_Adminhtml
 * @copyright  Copyright (c) 2004-2007 Irubin Consulting Inc. DBA Varien (http://www.varien.com)
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * Product Stores tab
 *
 * @category   Mage
 * @package    Mage_Adminhtml
 * @author     Dmitriy Soroka <dmitriy@varien.com>
 * @author     Victor Tihonchuk <victor@varien.com>
 */
class Mage_Adminhtml_Block_Catalog_Product_Edit_Tab_Stores extends Mage_Adminhtml_Block_Store_Switcher
{
    protected $_storeFromHtml;
    
    public function __construct()
    {
        parent::__construct();
        $this->setTemplate('catalog/product/edit/stores.phtml');
    }
    
    public function getStoreId()
    {
        return Mage::registry('product')->getStoreId();
    }
    
    public function getProductId()
    {
        return Mage::registry('product')->getId();
    }
    
    public function isProductInStore($storeId)
    {
        return in_array($storeId, Mage::registry('product')->getStoreIds());
    }
    
    public function getStoreName($storeId)
    {
        return Mage::getModel('core/store')->load($storeId)->getName();
    }
    
    public function getChooseFromStoreHtml()
    {
        if (!$this->_storeFromHtml) {
            $stores = Mage::registry('product')->getStoreIds();
            $this->_storeFromHtml = '<select name="store_chooser">';
            $this->_storeFromHtml.= '<option value="0">'.Mage::helper('catalog')->__('Default Store').'</option>';
            foreach ($this->getWebsiteCollection() as $_website) {
                $showWebsite = false;
                foreach ($this->getGroupCollection($_website) as $_group) {
                    $showGroup = false;
                    foreach ($this->getStoreCollection($_group) as $_store) {
                        if (!in_array($_store->getId(), $stores)) {
                            continue;
                        }
                        if ($showWebsite == false) {
                            $showWebsite = true;
                            $this->_storeFromHtml .= '<optgroup label="' . $_website->getName() . '"></optgroup>';
                        }
                        if ($showGroup == false) {
                            $showGroup = true;
                            $this->_storeFromHtml .= '<optgroup label="&nbsp;&nbsp;&nbsp;&nbsp;' . $_group->getName() . '">';
                        }
                        $this->_storeFromHtml .= '<option value="' . $_store->getId() . '">&nbsp;&nbsp;&nbsp;&nbsp;' . $_store->getName() . '</option>';
                    }
                    if ($showGroup == true) {
                        $this->_storeFromHtml .= '</optgroup>';
                    }
                }
            }
            $this->_storeFromHtml.= '</select>';
        }
        return $this->_storeFromHtml;
    }
}
