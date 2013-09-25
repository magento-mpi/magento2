<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Catalog
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Widget to display catalog link
 *
 * @category   Magento
 * @package    Magento_Catalog
 * @author     Magento Core Team <core@magentocommerce.com>
 */

class Magento_Catalog_Block_Widget_Link
    extends Magento_Core_Block_Html_Link
    implements Magento_Widget_Block_Interface
{
    /**
     * Entity model name which must be used to retrieve entity specific data.
     * @var null|Magento_Catalog_Model_Resource_Abstract
     */
    protected $_entityResource = null;

    /**
     * Prepared href attribute
     *
     * @var string
     */
    protected $_href;

    /**
     * Prepared anchor text
     *
     * @var string
     */
    protected $_anchorText;

    /**
     * Store manager
     *
     * @var Magento_Core_Model_StoreManagerInterface
     */
    protected $_storeManager;

    /**
     * Url rewrite
     *
     * @var Magento_Core_Model_Resource_Url_Rewrite
     */
    protected $_urlRewrite;

    /**
     * Construct
     *
     * @param Magento_Core_Model_Resource_Url_Rewrite $urlRewrite
     * @param Magento_Core_Model_StoreManagerInterface $storeManager
     * @param Magento_Core_Helper_Data $coreData
     * @param Magento_Core_Block_Template_Context $context
     * @param array $data
     */
    public function __construct(
        Magento_Core_Model_Resource_Url_Rewrite $urlRewrite,
        Magento_Core_Model_StoreManagerInterface $storeManager,
        Magento_Core_Helper_Data $coreData,
        Magento_Core_Block_Template_Context $context,
        array $data = array()
    ) {
        $this->_urlRewrite = $urlRewrite;
        $this->_storeManager = $storeManager;
        parent::__construct($coreData, $context, $data);
    }

    /**
     * Prepare url using passed id path and return it
     * or return false if path was not found in url rewrites.
     *
     * @return string|false
     */
    public function getHref()
    {
        if (!$this->_href) {

            if($this->hasStoreId()) {
                $store = $this->_storeManager->getStore($this->getStoreId());
            } else {
                $store = $this->_storeManager->getStore();
            }

            /* @var $store Magento_Core_Model_Store */
            $href = "";
            if ($this->getData('id_path')) {
                $href = $this->_urlRewrite->getRequestPathByIdPath($this->getData('id_path'), $store);
                if (!$href) {
                    return false;
                }
            }

            $this->_href = $store->getUrl('', array('_direct' => $href));
        }

        if(strpos($this->_href, "___store") === false){
            $symbol = (strpos($this->_href, "?") === false) ? "?" : "&";
            $this->_href = $this->_href . $symbol . "___store=" . $store->getCode();
        }

        return $this->_href;
    }

    /**
     * Prepare anchor text using passed text as parameter.
     * If anchor text was not specified get entity name from DB.
     *
     * @return string
     */
    public function getAnchorText()
    {
        if (!$this->_anchorText && $this->_entityResource) {
            if (!$this->getData('anchor_text')) {
                $idPath = explode('/', $this->_getData('id_path'));
                if (isset($idPath[1])) {
                    $id = $idPath[1];
                    if ($id) {
                        $this->_anchorText = $this->_entityResource->getAttributeRawValue($id, 'name',
                            $this->_storeManager->getStore());
                    }
                }
            } else {
                $this->_anchorText = $this->getData('anchor_text');
            }
        }

        return $this->_anchorText;
    }

    /**
     * Render block HTML
     * or return empty string if url can't be prepared
     *
     * @return string
     */
    protected function _toHtml()
    {
        if ($this->getHref()) {
            return parent::_toHtml();
        }
        return '';
    }
}
