<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Tag
 * @copyright   {copyright}
 * @license     {license_link}
 */

class Magento_Tag_Block_Product_List extends Magento_Core_Block_Template
{
    protected $_collection;

    /**
     * Unique Html Id
     *
     * @var string
     */
    protected $_uniqueHtmlId = null;

    /**
     * Core url
     *
     * @var Magento_Core_Helper_Url
     */
    protected $_coreUrl = null;

    /**
     * @param Magento_Core_Helper_Url $coreUrl
     * @param Magento_Core_Helper_Data $coreData
     * @param Magento_Core_Block_Template_Context $context
     * @param array $data
     */
    public function __construct(
        Magento_Core_Helper_Url $coreUrl,
        Magento_Core_Helper_Data $coreData,
        Magento_Core_Block_Template_Context $context,
        array $data = array()
    ) {
        $this->_coreUrl = $coreUrl;
        parent::__construct($coreData, $context, $data);
    }

    public function getCount()
    {
        return count($this->getTags());
    }

    public function getTags()
    {
        return $this->_getCollection()->getItems();
    }

    public function getProductId()
    {
        if ($product = Mage::registry('current_product')) {
            return $product->getId();
        }
        return false;
    }

    protected function _getCollection()
    {
        if( !$this->_collection && $this->getProductId() ) {

            $model = Mage::getModel('Magento_Tag_Model_Tag');
            $this->_collection = $model->getResourceCollection()
                ->addPopularity()
                ->addStatusFilter($model->getApprovedStatus())
                ->addProductFilter($this->getProductId())
                ->setFlag('relation', true)
                ->addStoreFilter(Mage::app()->getStore()->getId())
                ->setActiveFilter()
                ->load();
        }
        return $this->_collection;
    }

    protected function _beforeToHtml()
    {
        if (!$this->getProductId()) {
            return false;
        }

        return parent::_beforeToHtml();
    }

    public function getFormAction()
    {
        $helper = $this->_coreUrl;
        return Mage::getUrl('tag/index/save', array(
            'product' => $this->getProductId(),
            Magento_Core_Controller_Front_Action::PARAM_NAME_URL_ENCODED => $helper->getEncodedUrl()
        ));
    }

    /**
     * Render tags by specified pattern and implode them by specified 'glue' string
     *
     * @param string $pattern
     * @param string $glue
     * @return string
     */
    public function renderTags($pattern, $glue = ' ')
    {
        $out = array();
        foreach ($this->getTags() as $tag) {
            $out[] = sprintf($pattern,
                $tag->getTaggedProductsUrl(), $this->escapeHtml($tag->getName()), $tag->getProducts()
            );
        }
        return implode($out, $glue);
    }

    /**
     * Generate unique html id
     *
     * @param string $prefix
     * @return string
     */
    public function getUniqueHtmlId($prefix = '')
    {
        if (is_null($this->_uniqueHtmlId)) {
            $this->_uniqueHtmlId = $this->_coreData->uniqHash($prefix);
        }
        return $this->_uniqueHtmlId;
    }
}
