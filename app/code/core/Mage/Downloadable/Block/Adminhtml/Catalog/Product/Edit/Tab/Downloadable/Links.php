<?php
/**
 * {license_notice}
 *
 * @category    Mage
 * @package     Mage_Downloadable
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Adminhtml catalog product downloadable items tab links section
 *
 * @category    Mage
 * @package     Mage_Downloadable
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Mage_Downloadable_Block_Adminhtml_Catalog_Product_Edit_Tab_Downloadable_Links
    extends Mage_Adminhtml_Block_Template
{
    /**
     * Block config data
     *
     * @var Varien_Object
     */
    protected $_config;

    /**
     * Purchased Separately Attribute cache
     *
     * @var Mage_Catalog_Model_Resource_Eav_Attribute
     */
    protected $_purchasedSeparatelyAttribute = null;

    protected $_template = 'product/edit/downloadable/links.phtml';

    /**
     * Class constructor
     *
     */
    protected function _construct()
    {
        parent::_construct();

        $this->setCanEditPrice(true);
        $this->setCanReadPrice(true);
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
     * Retrieve Purchased Separately Attribute object
     *
     * @return Mage_Catalog_Model_Resource_Eav_Attribute
     */
    public function getPurchasedSeparatelyAttribute()
    {
        if (is_null($this->_purchasedSeparatelyAttribute)) {
            $_attributeCode = 'links_purchased_separately';

            $this->_purchasedSeparatelyAttribute = Mage::getModel('Mage_Eav_Model_Entity_Attribute')
                ->loadByCode(Mage_Catalog_Model_Product::ENTITY, $_attributeCode);
        }

        return $this->_purchasedSeparatelyAttribute;
    }

    /**
     * Retrieve Purchased Separately HTML select
     *
     * @return string
     */
    public function getPurchasedSeparatelySelect()
    {
        $select = $this->getLayout()->createBlock('Mage_Adminhtml_Block_Html_Select')
            ->setName('product[links_purchased_separately]')
            ->setId('downloadable_link_purchase_type')
            ->setOptions(Mage::getSingleton('Mage_Adminhtml_Model_System_Config_Source_Yesno')->toOptionArray())
            ->setValue($this->getProduct()->getLinksPurchasedSeparately());

        return $select->getHtml();
    }

    /**
     * Retrieve Add button HTML
     *
     * @return string
     */
    public function getAddButtonHtml()
    {
        $addButton = $this->getLayout()->createBlock('Mage_Adminhtml_Block_Widget_Button')
            ->setData(array(
                'label' => Mage::helper('Mage_Downloadable_Helper_Data')->__('Add New Row'),
                'id'    => 'add_link_item',
                'class' => 'add'
            ));
        return $addButton->toHtml();
    }

    /**
     * Retrieve default links title
     *
     * @return string
     */
    public function getLinksTitle()
    {
        return Mage::getStoreConfig(Mage_Downloadable_Model_Link::XML_PATH_LINKS_TITLE);
    }

    /**
     * Check exists defined links title
     *
     * @return bool
     */
    public function getUsedDefault()
    {
        return $this->getProduct()->getAttributeDefaultValue('links_title') === false;
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
        $links = $this->getProduct()->getTypeInstance()->getLinks($this->getProduct());
        $priceWebsiteScope = $this->getIsPriceWebsiteScope();
        $fileHelper = Mage::helper('Mage_Downloadable_Helper_File');
        foreach ($links as $item) {
            $tmpLinkItem = array(
                'link_id' => $item->getId(),
                'title' => $this->escapeHtml($item->getTitle()),
                'price' => $this->getCanReadPrice() ? $this->getPriceValue($item->getPrice()) : '',
                'number_of_downloads' => $item->getNumberOfDownloads(),
                'is_shareable' => $item->getIsShareable(),
                'link_url' => $item->getLinkUrl(),
                'link_type' => $item->getLinkType(),
                'sample_file' => $item->getSampleFile(),
                'sample_url' => $item->getSampleUrl(),
                'sample_type' => $item->getSampleType(),
                'sort_order' => $item->getSortOrder(),
            );
            $file = $fileHelper->getFilePath(
                Mage_Downloadable_Model_Link::getBasePath(), $item->getLinkFile()
            );

            if ($item->getLinkFile() && !is_file($file)) {
                Mage::helper('Mage_Core_Helper_File_Storage_Database')->saveFileToFilesystem($file);
            }

            if ($item->getLinkFile() && is_file($file)) {
                $name = '<a href="'
                    . $this->getUrl('*/downloadable_product_edit/link', array(
                        'id' => $item->getId(),
                        '_secure' => true
                    )) . '">' . $fileHelper->getFileFromPathFile($item->getLinkFile()) . '</a>';
                $tmpLinkItem['file_save'] = array(
                    array(
                        'file' => $item->getLinkFile(),
                        'name' => $name,
                        'size' => filesize($file),
                        'status' => 'old'
                    ));
            }
            $sampleFile = $fileHelper->getFilePath(
                Mage_Downloadable_Model_Link::getBaseSamplePath(), $item->getSampleFile()
            );
            if ($item->getSampleFile() && is_file($sampleFile)) {
                $tmpLinkItem['sample_file_save'] = array(
                    array(
                        'file' => $item->getSampleFile(),
                        'name' => $fileHelper->getFileFromPathFile($item->getSampleFile()),
                        'size' => filesize($sampleFile),
                        'status' => 'old'
                    ));
            }
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
     * Return formatted price with two digits after decimal point
     *
     * @param decimal $value
     * @return decimal
     */
    public function getPriceValue($value)
    {
        return number_format($value, 2, null, '');
    }

    /**
     * Retrieve max downloads value from config
     *
     * @return int
     */
    public function getConfigMaxDownloads()
    {
        return Mage::getStoreConfig(Mage_Downloadable_Model_Link::XML_PATH_DEFAULT_DOWNLOADS_NUMBER);
    }

    /**
     * Prepare block Layout
     *
     */
    protected function _prepareLayout()
    {
        $this->addChild('upload_button', 'Mage_Adminhtml_Block_Widget_Button', array(
            'id'      => '',
            'label'   => Mage::helper('Mage_Adminhtml_Helper_Data')->__('Upload Files'),
            'type'    => 'button',
            'onclick' => 'Downloadable.massUploadByType(\'links\');Downloadable.massUploadByType(\'linkssample\')'
        ));
    }

    /**
     * Retrieve Upload button HTML
     *
     * @return string
     */
    public function getUploadButtonHtml()
    {
        return $this->getChildBlock('upload_button')->toHtml();
    }

    /**
     * Retrieve File Field Name
     *
     * @param string $type
     * @return string
     */
    public function getFileFieldName($type)
    {
        return $type;
    }


    /**
     * Retrieve Upload URL
     *
     * @param string $type
     * @return string
     */
    public function getUploadUrl($type)
    {
        return Mage::getModel('Mage_Adminhtml_Model_Url')->addSessionParam()
            ->getUrl('*/downloadable_file/upload', array('type' => $type, '_secure' => true));
    }

    /**
     * Retrieve config json
     *
     * @param string $type
     * @return string
     */
    public function getConfigJson($type = 'links')
    {
        $this->getConfig()->setUrl($this->getUploadUrl($type));
        $this->getConfig()->setParams(array('form_key' => $this->getFormKey()));
        $this->getConfig()->setFileField($this->getFileFieldName($type));
        $this->getConfig()->setFilters(array(
            'all'    => array(
                'label' => Mage::helper('Mage_Adminhtml_Helper_Data')->__('All Files'),
                'files' => array('*.*')
            )
        ));
        $this->getConfig()->setReplaceBrowseWithRemove(true);
        $this->getConfig()->setWidth('32');
        $this->getConfig()->setHideUploadButton(true);
        return Mage::helper('Mage_Core_Helper_Data')->jsonEncode($this->getConfig()->getData());
    }

    /**
     * Retrieve config object
     *
     * @return Varien_Object
     */
    public function getConfig()
    {
        if (is_null($this->_config)) {
            $this->_config = new Varien_Object();
        }

        return $this->_config;
    }
}
