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
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade Magento to newer
 * versions in the future. If you wish to customize Magento for your
 * needs please refer to http://www.magentocommerce.com for more information.
 *
 * @category    Mage
 * @package     Mage_Downloadable
 * @copyright   Copyright (c) 2008 Irubin Consulting Inc. DBA Varien (http://www.varien.com)
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * Adminhtml catalog product downloadable items tab links section
 *
 * @category    Mage
 * @package     Mage_Downloadable
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Mage_Downloadable_Block_Adminhtml_Catalog_Product_Edit_Tab_Downloadable_Links extends Mage_Adminhtml_Block_Template
{

    /**
     * Enter description here...
     *
     * @var unknown_type
     */
    protected $_purchasedSeparatelyAttribute = null;

    /**
     * Class constructor
     *
     */
    public function __construct()
    {
        parent::__construct();
        $this->setTemplate('downloadable/product/edit/downloadable/links.phtml');
    }

    /**
     * Get product that is being edited
     *
     * @return Mage_Catalog_Model_Product
     */
    public function getProduct()
    {
        return Mage::registry('product');
    }

    /**
     * Enter description here...
     *
     * @return Mage_Eav_Model_Entity_Attribute
     */
    public function getPurchasedSeparatelyAttribute()
    {
        if (is_null($this->_purchasedSeparatelyAttribute)) {
            $_attributeCode = 'links_purchased_separately';

            $this->_purchasedSeparatelyAttribute = Mage::getModel('eav/entity_attribute')
                ->loadByCode('catalog_product', $_attributeCode);
        }

        return $this->_purchasedSeparatelyAttribute;
    }

    /**
     * Enter description here...
     *
     * @return string
     */
    public function getPurchasedSeparatelySelect()
    {
        $select = $this->getLayout()->createBlock('adminhtml/html_select')
            ->setName('product[links_purchased_separately]')
            ->setOptions(Mage::getSingleton('adminhtml/system_config_source_yesno')->toOptionArray())
            ->setValue($this->getProduct()->getLinksPurchasedSeparately());

        return $select->getHtml();
    }

    /**
     * Enter description here...
     *
     * @return string
     */
    public function getAddButtonHtml()
    {
        $addButton = $this->getLayout()->createBlock('adminhtml/widget_button')
            ->setData(array(
                'label' => Mage::helper('downloadable')->__('Add New Row'),
                'id' => 'add_link_item',
                'class' => 'add',
            ));
        return $addButton->toHtml();
    }

    public function getLinksTitle()
    {
        return Mage::getStoreConfig('downloadable/options/links_title');
    }

    public function getUsedDefault()
    {
        return is_null($this->getProduct()->getAttributeDefaultValue('links_title'));
    }

    /**
     * Return true if price in website scope
     *
     * @return bool
     */
    public function getIsPriceWebsiteScope()
    {
        $scope =  (int) Mage::app()->getStore()->getConfig(Mage_Core_Model_Store::XML_PATH_PRICE_SCOPE);
        if ($scope == Mage_Core_Model_Store::PRICE_SCOPE_WEBSITE) {
            return true;
        }
        return false;
    }

    /**
     * Return array of links
     *
     * @return array
     */
    public function getLinkData()
    {
        $linkArr = array();
        $links = $this->getProduct()->getTypeInstance()->getLinks();
        $priceWebsiteScope = $this->getIsPriceWebsiteScope();
        foreach ($links as $item) {
            $tmpLinkItem = array(
                'link_id' => $item->getId(),
                'title' => $item->getTitle(),
                'price' => $this->getPriceValue($item->getPrice()),
                'number_of_downloads' => $item->getNumberOfDownloads(),
                'is_shareable' => $item->getIsShareable(),
                'link_url' => $item->getLinkUrl(),
                'sample_url' => $item->getSampleUrl(),
                'sort_order' => $item->getSortOrder()
            );
            if ($item->getNumberOfDownloads() == '0') {
                $tmpLinkItem['is_unlimited'] = ' checked="checked"';
            }
            if ($this->getProduct()->getStoreId() && $item->getStoreTitle()) {
                $tmpLinkItem['store_title'] = $item->getStoreTitle();
            }
            if ($this->getProduct()->getStoreId() && $priceWebsiteScope) {
                $tmpLinkItem['website_price'] = $item->getWebsitePrice();
            }
            $linkArr[] = new Varien_Object($tmpLinkItem);
        }
        return $linkArr;
    }

    /**
     * Return formated price with two digits after decimal point
     *
     * @param decimal $value
     * @return decimal
     */
    public function getPriceValue($value)
    {
        return number_format($value, 2, null, '');
    }

    public function getConfigMaxDownloads()
    {
        return Mage::getStoreConfig('downloadable/options/downloads_number');
    }

}
