<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Checkout
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Shopping cart api for product
 *
 * @category    Magento
 * @package     Magento_Checkout
 * @author      Magento Core Team <core@magentocommerce.com>
 */

namespace Magento\Checkout\Model\Cart\Product\Api;

class V2 extends \Magento\Checkout\Model\Cart\Product\Api
{

    /**
     * Return an Array of Object attributes.
     *
     * @param Mixed $data
     * @return Array
     */
    protected function _prepareProductsData($data){
        if (is_object($data)) {
            $arr = get_object_vars($data);
            foreach ($arr as $key => $value) {
                $assocArr = array();
                if (is_array($value)) {
                    foreach ($value as $v) {
                        if (is_object($v) && count(get_object_vars($v))==2
                            && isset($v->key) && isset($v->value)) {
                            $assocArr[$v->key] = $v->value;
                        }
                    }
                }
                if (!empty($assocArr)) {
                    $arr[$key] = $assocArr;
                }
            }
            $arr = $this->_prepareProductsData($arr);
            return parent::_prepareProductsData($arr);
        }
        if (is_array($data)) {
            foreach ($data as $key => $value) {
                if (is_object($value) || is_array($value)) {
                    $data[$key] = $this->_prepareProductsData($value);
                } else {
                    $data[$key] = $value;
                }
            }
            return parent::_prepareProductsData($data);
        }
        return $data;
    }
}
