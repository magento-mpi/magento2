<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Adminhtml
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Adminhtml block for fieldset of configurable product
 */
namespace Magento\Catalog\Block\Adminhtml\Product\Composite\Fieldset;

class Configurable extends \Magento\Catalog\Block\Product\View\Type\Configurable
{
    /**
     * Retrieve product
     *
     * @return \Magento\Catalog\Model\Product
     */
    public function getProduct()
    {
        if (!$this->hasData('product')) {
            $this->setData('product', $this->_coreRegistry->registry('product'));
        }
        $product = $this->getData('product');
        if (is_null($product->getTypeInstance()->getStoreFilter($product))) {
            $product->getTypeInstance()->setStoreFilter(
                $this->_storeManager->getStore($product->getStoreId()),
                $product
            );
        }

        return $product;
    }

    /**
     * Retrieve current store
     *
     * @return \Magento\Core\Model\Store
     */
    public function getCurrentStore()
    {
        return $this->_storeManager->getStore($this->getProduct()->getStoreId());
    }

    /**
     * Returns additional values for js config, con be overridden by descedants
     *
     * @return array
     */
    protected function _getAdditionalConfig()
    {
        $result = parent::_getAdditionalConfig();
        $result['disablePriceReload'] = true; // There's no field for price at popup
        $result['stablePrices'] = true; // We don't want to recalc prices displayed in OPTIONs of SELECT
        return $result;
    }
}
