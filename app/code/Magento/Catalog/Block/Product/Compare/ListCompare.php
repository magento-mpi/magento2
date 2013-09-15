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
 * Catalog products compare block
 *
 * @category   Magento
 * @package    Magento_Catalog
 * @author     Magento Core Team <core@magentocommerce.com>
 */
namespace Magento\Catalog\Block\Product\Compare;

class ListCompare extends \Magento\Catalog\Block\Product\Compare\AbstractCompare
{
    /**
     * Product Compare items collection
     *
     * @var \Magento\Catalog\Model\Resource\Product\Compare\Item\Collection
     */
    protected $_items;

    /**
     * Compare Products comparable attributes cache
     *
     * @var array
     */
    protected $_attributes;

    /**
     * Flag which allow/disallow to use link for as low as price
     *
     * @var bool
     */
    protected $_useLinkForAsLowAs = false;

    /**
     * Customer id
     *
     * @var null|int
     */
    protected $_customerId = null;

    /**
     * Default MAP renderer type
     *
     * @var string
     */
    protected $_mapRenderer = 'msrp_noform';

    /**
     * Retrieve url for adding product to wishlist with params
     *
     * @param \Magento\Catalog\Model\Product $product
     * @return string
     */
    public function getAddToWishlistUrl($product)
    {
        $continueUrl    = $this->_coreData->urlEncode($this->getUrl('customer/account'));
        $urlParamName   = \Magento\Core\Controller\Front\Action::PARAM_NAME_URL_ENCODED;

        $params = array(
            $urlParamName   => $continueUrl
        );

        return $this->helper('Magento\Wishlist\Helper\Data')->getAddUrlWithParams($product, $params);
    }

    /**
     * Preparing layout
     *
     * @return \Magento\Catalog\Block\Product\Compare\ListCompare
     */
    protected function _prepareLayout()
    {
        $headBlock = $this->getLayout()->getBlock('head');
        if ($headBlock) {
            $headBlock->setTitle(__('Products Comparison List') . ' - ' . $headBlock->getDefaultTitle());
        }
        return parent::_prepareLayout();
    }

    /**
     * Retrieve Product Compare items collection
     *
     * @return \Magento\Catalog\Model\Resource\Product\Compare\Item\Collection
     */
    public function getItems()
    {
        if (is_null($this->_items)) {
            $this->_catalogProductCompare->setAllowUsedFlat(false);

            $this->_items = \Mage::getResourceModel('Magento\Catalog\Model\Resource\Product\Compare\Item\Collection')
                ->useProductItem(true)
                ->setStoreId(\Mage::app()->getStore()->getId());

            if (\Mage::getSingleton('Magento\Customer\Model\Session')->isLoggedIn()) {
                $this->_items->setCustomerId(\Mage::getSingleton('Magento\Customer\Model\Session')->getCustomerId());
            } elseif ($this->_customerId) {
                $this->_items->setCustomerId($this->_customerId);
            } else {
                $this->_items->setVisitorId(\Mage::getSingleton('Magento\Log\Model\Visitor')->getId());
            }

            $this->_items
                ->addAttributeToSelect(\Mage::getSingleton('Magento\Catalog\Model\Config')->getProductAttributes())
                ->loadComparableAttributes()
                ->addMinimalPrice()
                ->addTaxPercents()
                ->setVisibility(\Mage::getSingleton('Magento\Catalog\Model\Product\Visibility')->getVisibleInSiteIds());
        }

        return $this->_items;
    }

    /**
     * Retrieve Product Compare Attributes
     *
     * @return array
     */
    public function getAttributes()
    {
        if (is_null($this->_attributes)) {
            $this->_attributes = $this->getItems()->getComparableAttributes();
        }

        return $this->_attributes;
    }

    /**
     * Retrieve Product Attribute Value
     *
     * @param \Magento\Catalog\Model\Product $product
     * @param \Magento\Catalog\Model\Resource\Eav\Attribute $attribute
     * @return string
     */
    public function getProductAttributeValue($product, $attribute)
    {
        if (!$product->hasData($attribute->getAttributeCode())) {
            return __('N/A');
        }

        if ($attribute->getSourceModel()
            || in_array($attribute->getFrontendInput(), array('select','boolean','multiselect'))
        ) {
            //$value = $attribute->getSource()->getOptionText($product->getData($attribute->getAttributeCode()));
            $value = $attribute->getFrontend()->getValue($product);
        } else {
            $value = $product->getData($attribute->getAttributeCode());
        }
        return ((string)$value == '') ? __('No') : $value;
    }

    /**
     * Retrieve Print URL
     *
     * @return string
     */
    public function getPrintUrl()
    {
        return $this->getUrl('*/*/*', array('_current'=>true, 'print'=>1));
    }

    /**
     * Setter for customer id
     *
     * @param int $id
     * @return \Magento\Catalog\Block\Product\Compare\ListCompare
     */
    public function setCustomerId($id)
    {
        $this->_customerId = $id;
        return $this;
    }
}
