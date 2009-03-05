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
 * @category   Mage
 * @package    Mage_Api
 * @copyright  Copyright (c) 2008 Irubin Consulting Inc. DBA Varien (http://www.varien.com)
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * Webservices server handler v2
 *
 * @category   Mage
 * @package    Mage_Api
 * @author     Magento Core Team <core@magentocommerce.com>
 */
class Mage_Api_Model_Server_V2_Handler extends Mage_Api_Model_Server_Handler_Abstract
{
    protected $_resourceSuffix = '_v2';

    /**
     * Enter description here...
     *
     * @param unknown_type $object
     * @return unknown
     */
    private function _object2Array( $object )
    {
        foreach ( $object as $key => $value ) {
            if ( is_object( $value ) ) {
                $object->$key = $this->_object2Array( $value );
            }
        }
        return (array)$object;
    }


    /**
     * Interceptor for all interfaces
     *
     * @param sttring $function
     * @param array $args
     */

    public function __call( $function, $args )
    {
        $sessionId = array_shift( $args );
        $argsAsArray = $this->_object2Array( $args );
        $apiKey = '';
        $config = Mage::getConfig();
        foreach ($config->getNode('api/v2/resources_function_prefix')->children() as $resource => $prefix) {
            $prefix = $prefix->asArray();
            if (false !== strpos($function, $prefix)) {
                $method = substr($function, strlen($prefix));
                $apiKey = $resource . '.' . strtolower($method[0]).substr($method, 1);
            }
        }
        return $this->call($sessionId, $apiKey, $argsAsArray);
    }


//    public function catalogCategoryTree( $sessionId, $parentId=null, $storeView=null )
//    {
//        $args = array();
//        if (!is_null($parentId)) {
//            $args[0] = $parentId;
//        }
//        if (!is_null($storeView)) {
//            $args[1] = $storeView;
//        }
//        return $this->call($sessionId, "category.tree", $args);
//    }
//
//    public function catalogCategoryCurrentStore( $sessionId, $store = null )
//    {
//        $args = array();
//        if (!is_null($store)) {
//            $args[0] = $store;
//        }
//        return $this->call($sessionId, "category.currentStore", $args);
//    }
//
//    public function catalogCategoryLevel( $sessionId, $website=null, $storeView=null, $parentCategory=null )
//    {
//        $args = array();
//        if (!is_null($website)) {
//            $args[0] = $website;
//        }
//        if (!is_null($storeView)) {
//            $args[1] = $storeView;
//        }
//        if (!is_null($parentCategory)) {
//            $args[2] = $parentCategory;
//        }
//        return $this->call($sessionId, "category.level", $args);
//    }
//
//    public function catalogCategoryInfo( $sessionId, $categoryId, $storeView=null, $attributes=null )
//    {
//        $args = array();
//        $args[0] = $categoryId;
//        if (!is_null($storeView)) {
//            $args[1] = $storeView;
//        }
//        if (!is_null($attributes)) {
//            $args[2] = $attributes;
//        }
//        return $this->call($sessionId, "category.info", $args);
//    }
//
//    public function catalogCategoryCreate($sessionId, $parentId, $categoryData, $storeView = null)
//    {
//        $args = array();
//        $args[0] = $parentId;
//        $_categoryData = array();
//        foreach( $categoryData as $k=>$v )  {
//            $_categoryData[ $k ] = $v;
//        }
//        $args[1] = $_categoryData;
//        if (!is_null($storeView))   {
//            $args[2] = $storeView;
//        }
//        return $this->call($sessionId, "category.create", $args);
//    }
//
//    public function catalogCategoryUpdate($sessionId, $categoryId, $categoryData, $storeView = null)
//    {
//        $args = array();
//        $args[0] = $categoryId;
//        $_categoryData = array();
//        foreach( $categoryData as $k=>$v )  {
//            $_categoryData[ $k ] = $v;
//        }
//        $args[1] = $_categoryData;
//        if (!is_null($storeView))   {
//            $args[2] = $storeView;
//        }
//        return $this->call($sessionId, "category.update", $args);
//    }
//
//    public function catalogCategoryMove( $sessionId, $categoryId, $parentId, $afterId=null )
//    {
//        $args = array();
//        $args[0] = $categoryId;
//        $args[1] = $parentId;
//        if (!is_null($afterId)) {
//            $args[2] = $afterId;
//        }
//        return $this->call($sessionId, "category.move", $args);
//    }
//
//    public function catalogCategoryDelete( $sessionId, $categoryId )
//    {
//        $args = array();
//        $args[0] = $categoryId;
//        return $this->call($sessionId, "category.delete", $args);
//    }
//
//    public function catalogCategoryAssignedProducts( $sessionId, $categoryId )
//    {
//        $args = array();
//        $args[0] = $categoryId;
//        return $this->call($sessionId, "category.assignedProducts", $args);
//    }
//
//    public function catalogCategoryAssignProduct( $sessionId, $categoryId, $product, $position=null)
//    {
//        $args = array();
//        $args[0] = $categoryId;
//        $args[1] = $product;
//        if (!is_null($position))    {
//            $args[2] = $position;
//        }
//        return $this->call($sessionId, "category.assignProduct", $args);
//    }
//
//    public function catalogCategoryUpdateProduct( $sessionId, $categoryId, $product, $position=null)
//    {
//        $args = array();
//        $args[0] = $categoryId;
//        $args[1] = $product;
//        if (!is_null($position))    {
//            $args[2] = $position;
//        }
//        return $this->call($sessionId, "category.updateProduct", $args);
//    }
//
//    public function catalogCategoryRemoveProduct( $sessionId, $categoryId, $product )
//    {
//        $args = array();
//        $args[0] = $categoryId;
//        $args[1] = $product;
//        return $this->call($sessionId, "category.removeProduct", $args);
//    }
//
//    public function customerCustomerList( $sessionId, $filters = null)
//    {
//        $args = array();
//        if (!is_null( $filters ))   {
//            $args[0] = $filters;
//        }
//        return $this->call($sessionId, "customer.list", $args);
//    }
//
//    public function customerCustomerCreate( $sessionId, $customerData )
//    {
//        $args = array();
//        $cDataArray = array();
//        foreach( $customerData as $k=>$v )  {
//            $cDataArray[ $k ] = $v;
//        }
//        $args[0] = $cDataArray;
//        return $this->call($sessionId, "customer.create", $args);
//    }
//
//    public function customerCustomerInfo( $sessionId, $customerId )
//    {
//        $args = array();
//        $args[0] = $customerId;
//        return $this->call($sessionId, "customer.info", $args);
//    }
//
//    public function customerCustomerUpdate( $sessionId, $customerId, $customerData )
//    {
//        $args = array();
//        $args[0] = $customerId;
//        $cDataArray = array();
//        foreach( $customerData as $k=>$v )  {
//            $cDataArray[ $k ] = $v;
//        }
//        $args[1] = $cDataArray;
//        return $this->call($sessionId, "customer.update", $args);
//    }
//
//    public function customerCustomerDelete ( $sessionId, $customerId )
//    {
//        $args = array();
//        $args[0] = $customerId;
//        return $this->call($sessionId, "customer.delete", $args);
//    }
//
//    public function customerGroupList( $sessionId )
//    {
//        return $this->call($sessionId, "customer_group.list", array());
//    }
//
//    public function customerAddressList( $sessionId, $customerId )
//    {
//        $args = array();
//        $args[0] = $customerId;
//        return $this->call($sessionId, "customer_address.list", $args);
//    }
//
//
//    public function customerAddressCreate( $sessionId, $customerId, $addressData)
//    {
//        $args = array();
//        $args[0] = $customerId;
//        $cDataArray = array();
//        foreach( $addressData as $k=>$v )  {
//            $cDataArray[ $k ] = $v;
//        }
//        $args[1] = $cDataArray;
//        return $this->call($sessionId, "customer_address.create", $args);
//    }
//
//    public function customerAddressInfo ( $sessionId, $addressId )
//    {
//        $args = array();
//        $args[0] = $addressId;
//        return $this->call($sessionId, "customer_address.info", $args);
//    }
//
//    public function customerAddressUpdate( $sessionId, $addressId, $addressData )
//    {
//        $args = array();
//        $args[0] = $addressId;
//        $cDataArray = array();
//        foreach( $addressData as $k=>$v )  {
//            $cDataArray[ $k ] = $v;
//        }
//        $args[1] = $cDataArray;
//        return $this->call($sessionId, "customer_address.update", $args);
//    }
//
//    public function customerAddressDelete( $sessionId, $addressId )
//    {
//        $args = array();
//        $args[0] = $addressId;
//        return $this->call($sessionId, "customer_address.delete", $args);
//    }
//
//    public function directoryCountryList($sessionId)
//    {
//        return $this->call($sessionId, "country.list", array());
//    }
//
//    public function directoryRegionList( $sessionId, $country )
//    {
//        $args = array();
//        $args[0] = $country;
//        return $this->call($sessionId, "region.list", $args);
//    }
//
//    public function catalogCategoryAttributeCurrentStore( $sessionId, $storeView=null )
//    {
//        $args = array();
//        if (!is_null($storeView))   {
//            $args[0] = $storeView;
//        }
//        return $this->call($sessionId, "category_attribute.currentStore", $args);
//    }
//
//    public function catalogCategoryAttributeList( $sessionId )
//    {
//        return $this->call($sessionId, "category_attribute.list", array());
//    }
//
//
//    public function catalogCategoryAttributeOptions( $sessionId, $attributeId, $storeView=null )
//    {
//        $args = array();
//        $args[0] = $attributeId;
//        if (!is_null($storeView))   {
//            $args[1] = $storeView;
//        }
//        return $this->call($sessionId, "category_attribute.options", $args);
//    }
//
//
//
//    public function catalogProductCurrentStore( $sessionId, $storeView = null )
//    {
//        $args = array();
//        if (!is_null( $storeView )) {
//            $args[0] = $storeView;
//        }
//        return $this->call($sessionId, "product.currentStore", $args);
//    }
//
//
//    public function catalogProductList( $sessionId, $filters = null, $storeView = null )
//    {
//        $args = array();
//        if (!is_null($filters)) {
//            $args[0] = $filters;
//        }
//        if (!is_null($storeView)) {
//            $args[1] = $storeView;
//        }
//        return $this->call($sessionId, "product.list", $args);
//    }
//
//
//    public function catalogProductInfo( $sessionId, $product, $storeView=null, $attributes=null)
//    {
//        $args = array();
//        $args[0] = $product;
//        if (!is_null($storeView)) {
//            $args[1] = $storeView;
//        }
//        if (!is_null($attributes)) {
//            $args[2] = $attributes;
//        }
//        return $this->call($sessionId, "product.info", $args);
//    }
//
//
//    public function catalogProductCreate( $sessionId, $type, $set, $sku, $productData )
//    {
//        $args = array();
//        $args[0] = $type;
//        $args[1] = $set;
//        $args[2] = $sku;
//
//        $prodArray = array();
//        foreach( $productData as $k=>$v)    {
//            $prodArray[$k] = $v;
//        }
//        $args[3] = $prodArray;
//
//        return $this->call($sessionId, "product.create", $args);
//    }
//
//    public function catalogProductUpdate( $sessionId, $product, $productData, $storeView = null )
//    {
//        $args = array();
//        $args[0] = $product;
//
//        $prodArray = array();
//        foreach($productData as $k=>$v) {
//            $prodArray[$k] = $v;
//        }
//        $args[1] = $prodArray;
//
//        if (!is_null($storeView))   {
//            $args[2] = $storeView;
//        }
//        return $this->call($sessionId, "product.update", $args);
//    }
//
//    public function catalogProductSetSpecialPrice( $sessionId, $product, $specialPrice=null, $fromDate=null, $toDate=null, $storeView=null)
//    {
//        $args = array();
//        $args[0] = $product;
//        if (!is_null($specialPrice))    {
//            $args[1] = $specialPrice;
//        }
//        if (!is_null($fromDate))    {
//            $args[2] = $fromDate;
//        }
//        if (!is_null($toDate))    {
//            $args[3] = $toDate;
//        }
//        if (!is_null($storeView))    {
//            $args[4] = $storeView;
//        }
//        return $this->call($sessionId, "product.setSpecialPrice", $args);
//    }
//
//    public function catalogProductGetSpecialPrice( $sessionId, $product, $storeView=null)
//    {
//        $args = array();
//        $args[0] = $product;
//        if (!is_null($storeView))    {
//            $args[1] = $storeView;
//        }
//        return $this->call($sessionId, "product.getSpecialPrice", $args);
//    }
//
//    public function catalogProductDelete( $sessionId, $product )
//    {
//        $args = array();
//        $args[0] = $product;
//        return $this->call($sessionId, "product.delete", $args);
//    }
//
//
//
//
//    public function catalogProductAttributeCurrentStore( $sessionId, $storeView = null )
//    {
//        $args = array();
//        if (!is_null($storeView))   {
//            $args[0] = $storeView;
//        }
//        return $this->call($sessionId, "product_attribute.currentStore", $args);
//    }
//
//    public function catalogProductAttributeList( $sessionId, $setId )
//    {
//         $args = array();
//         $args[0] = $setId;
//         return $this->call($sessionId, "product_attribute.list", $args);
//    }
//
//    public function catalogProductAttributeOptions ( $sessionId, $attributeId, $storeView = null)
//    {
//         $args = array();
//         $args[0] = $attributeId;
//         if (!is_null($storeView))   {
//             $args[1] = $storeView;
//         }
//         return $this->call($sessionId, "product_attribute.options", $args);
//    }
//
//
//    public function catalogProductAttributeSetList( $sessionId )
//    {
//        return $this->call($sessionId, "product_attribute_set.list", array());
//    }
//
//
//    public function catalogProductTypeList( $sessionId )
//    {
//        return $this->call($sessionId, "product_type.list", array());
//    }
//
//    public function catalogProductAttributeTierPriceInfo( $sessionId, $sku )
//    {
//         $args = array();
//         $args[0] = $sku;
//         return $this->call($sessionId, "product_tier_price.info", $args);
//    }
//
//
//    public function catalogProductAttributeTierPriceUpdate( $sessionId, $sku, $tier = null )
//    {
//        $args = array();
//        $args[0] = $sku;
//        if (!is_null($tier))    {
//            foreach($tier as $index=>$tierPriceObject)  {
//                $newTierArr = array();
//                foreach($tierPriceObject as $k=>$v) {
//                    $newTierArr[$k] = $v;
//                }
//                $tier[$index] = $newTierArr;
//            }
//            $args[1] = $tier;
//        }
//
//        $fp = fopen('tpu.mylog', "w");
//        fwrite( $fp, serialize($args));
//        fclose( $fp );
//
//        return $this->call($sessionId, "product_tier_price.update", $args);
//    }
//
//
//    public function catalogProductAttributeMediaCurrentStore( $sessionId, $storeView = null )
//    {
//        $args = array();
//        if (!is_null($storeView))   {
//            $args[0] = $storeView;
//        }
//        return $this->call($sessionId, "product_attribute_media.currentStore", $args);
//    }
//
//
//    public function catalogProductAttributeMediaList( $sessionId, $product, $storeView = null)
//    {
//        $args = array();
//        $args[0] = $product;
//        if (!is_null( $storeView )) {
//            $args[1] = $storeView;
//        }
//        return $this->call($sessionId, "product_attribute_media.list", $args);
//    }
//
//
//    public function catalogProductAttributeMediaInfo( $sessionId, $product, $image, $storeView = null )
//    {
//        $args = array();
//        $args[0] = $product;
//        $args[1] = $image;
//        if (!is_null( $storeView )) {
//            $args[2] = $storeView;
//        }
//        return $this->call($sessionId, "product_attribute_media.info", $args);
//    }
//
//
//    public function catalogProductAttributeMediaTypes( $sessionId, $setId )
//    {
//        $args = array();
//        $args[0] = $setId;
//        return $this->call($sessionId, "product_attribute_media.types", $args);
//    }
//
//    public function catalogProductAttributeMediaCreate( $sessionId, $product, $data, $storeView = null)
//    {
//        $args = array();
//        $args[0] = $product;
//        $dataArray = array();
//        foreach ($data as $k=>$v)   {
//            if ( $k == 'file' ) {
//                $fileArray = array();
//                foreach ($v as $key=>$value)    {
//                    $fileArray[$key] = $value;
//                }
//                $dataArray[$k] = $fileArray;
//            }
//            else {
//                $dataArray[$k] = $v;
//            }
//        }
//        $args[1] = $dataArray;
//        if (!is_null( $storeView )) {
//            $args[2] = $storeView;
//        }
//        return $this->call($sessionId, "product_attribute_media.create", $args);
//    }
//
//
//    public function catalogProductAttributeMediaUpdate( $sessionId, $product, $file, $data, $storeView = null )
//    {
//        $args = array();
//        $args[0] = $product;
//        $args[1] = $file;
//        $dataArray = array();
//        foreach ($data as $k=>$v)   {
//            if ( $k == 'file' ) {
//                $fileArray = array();
//                foreach ($v as $key=>$value)    {
//                    $fileArray[$key] = $value;
//                }
//                $dataArray[$k] = $fileArray;
//            }
//            else {
//                $dataArray[$k] = $v;
//            }
//        }
//        $args[2] = $dataArray;
//        if (!is_null( $storeView )) {
//            $args[3] = $storeView;
//        }
//        return $this->call($sessionId, "product_attribute_media.update", $args);
//    }
//
//
//    public function catalogProductAttributeMediaRemove( $sessionId, $product, $file )
//    {
//        $args = array();
//        $args[0] = $product;
//        $args[1] = $file;
//        return $this->call($sessionId, "product_attribute_media.remove", $args);
//    }
//
//
//    public function catalogProductLinkList( $sessionId, $type, $product )
//    {
//        $args = array();
//        $args[0] = $type;
//        $args[1] = $product;
//        return $this->call($sessionId, "product_link.list", $args);
//    }
//
//    public function catalogProductLinkAssign( $sessionId, $type, $product, $linkedProduct, $data )
//    {
//        $args = array();
//        $args[0] = $type;
//        $args[1] = $product;
//        $args[2] = $linkedProduct;
//
//        $dataArray = array();
//        foreach( $date as $k=>$v )  {
//
//        }
//        $args[3] = $dataArray;
//        return $this->call($sessionId, 'product_link.assign', $args);
//    }
//
//
//    public function catalogProductLinkUpdate( $sessionId, $type, $product, $linkedProduct, $data )
//    {
//        $args = array();
//        $args[0] = $type;
//        $args[1] = $product;
//        $args[2] = $linkedProduct;
//
//        $dataArray = array();
//        foreach( $date as $k=>$v )  {
//
//        }
//        $args[3] = $dataArray;
//        return $this->call($sessionId, 'product_link.update', $args);
//    }
//
//
//    public function catalogProductLinkRemove( $sessionId, $type, $product, $linkedProduct )
//    {
//        $args = array();
//        $args[0] = $type;
//        $args[1] = $product;
//        $args[2] = $linkedProduct;
//        return $this->call($sessionId, 'product_link.remove', $args);
//    }
//
//    public function catalogProductLinkTypes ( $sessionId )
//    {
//        return $this->call($sessionId, 'product_link.types', array());
//    }
//
//    public function catalogProductLinkAttributes ($sessionId, $type )
//    {
//        $args = array();
//        $args[0] = $type;
//        return $this->call($sessionId, 'product_link.attributes', $args);
//    }
//
//
//    public function salesOrderList( $sessionId )
//    {
//        return $this->call($sessionId, 'order.list', array());
//    }
//
//
//    public function salesOrderInfo( $sessionId, $orderIncrementId )
//    {
//        $args = array();
//        $args[0] = $orderIncrementId;
//        return $this->call( $sessionId, 'order.info', $args );
//    }
//
//
//    public function salesOrderAddComment( $sessionId, $orderIncrementId, $status, $comment = null, $notify = null )
//    {
//        $args = array();
//        $args[0] = $orderIncrementId;
//        $args[1] = $status;
//        if (!is_null( $comment ))   {
//            $args[2] = $comment;
//        }
//        if (!is_null( $notify ))   {
//            $args[3] = $notify;
//        }
//        return $this->call( $sessionId, 'order.addComment', $args );
//    }
//
//    public function salesOrderHold( $sessionId, $orderIncrementId )
//    {
//        $args = array();
//        $args[0] = $orderIncrementId;
//        return $this->call( $sessionId, 'order.hold', $args );
//    }
//
//    public function salesOrderUnhold( $sessionId, $orderIncrementId )
//    {
//        $args = array();
//        $args[0] = $orderIncrementId;
//        return $this->call( $sessionId, 'order.unhold', $args );
//    }
//
//    public function salesOrderCancel( $sessionId, $orderIncrementId )
//    {
//        $args = array();
//        $args[0] = $orderIncrementId;
//        return $this->call( $sessionId, 'order.cancel', $args );
//    }
//
//    public function salesOrderShipmentList( $sessionId )
//    {
//        $args = array();
//        return $this->call( $sessionId, 'order_shipment.list', $args );
//    }
//
//    public function salesOrderShipmentInfo( $sessionId, $shipmentIncrementId )
//    {
//        $args = array();
//        $args[0] = $shipmentIncrementId;
//        return $this->call( $sessionId, 'order_shipment.info', $args );
//    }
//
//    public function salesOrderShipmentCreate( $sessionId, $orderIncrementId, $itemsQty, $comment=null, $email=null, $includeComment=null)
//    {
//        $args = array();
//        $args[0] = $orderIncrementId;
//        $args[1] = $itemsQty;
//        if (!is_null($comment)) {
//            $args[2] = $comment;
//        }
//        if (!is_null($email)) {
//            $args[3] = $email;
//        }
//        if (!is_null($includeComment)) {
//            $args[4] = $includeComment;
//        }
//        return $this->call( $sessionId, 'order_shipment.create', $args );
//    }
//
//    public function salesOrderShipmentAddComment( $sessionId, $shipmentIncrementId, $comment, $email=null, $includeInEmail=null)
//    {
//        $args = array();
//        $args[0] = $shipmentIncrementId;
//        $args[1] = $comment;
//        if (!is_null($email)) {
//            $args[2] = $email;
//        }
//        if (!is_null($includeInEmail)) {
//            $args[3] = $includeInEmail;
//        }
//        return $this->call( $sessionId, 'order_shipment.addComment', $args );
//    }
//
//    public function salesOrderShipmentAddTrack( $sessionId, $shipmentIncrementId, $carrier, $title, $trackNumber)
//    {
//        $args = array();
//        $args[0] = $shipmentIncrementId;
//        $args[1] = $carrier;
//        $args[2] = $title;
//        $args[3] = $trackNumber;
//        return $this->call( $sessionId, 'order_shipment.addTrack', $args );
//    }
//
//
//    public function salesOrderShipmentRemoveTrack( $sessionId, $shipmentIncrementId, $trackId)
//    {
//        $args = array();
//        $args[0] = $shipmentIncrementId;
//        $args[1] = $trackId;
//        return $this->call( $sessionId, 'order_shipment.removeTrack', $args );
//    }
//
//
//    public function salesOrderShipmentGetCarriers( $sessionId, $orderIncrementId)
//    {
//        $args = array();
//        $args[0] = $orderIncrementId;
//        return $this->call( $sessionId, 'order_shipment.getCarriers', $args );
//    }
//
//
//    public function salesOrderInvoiceList( $sessionId )
//    {
//        $args = array();
//        return $this->call( $sessionId, 'order_invoice.list', $args );
//    }
//
//    public function salesOrderInvoiceInfo( $sessionId, $invoiceIncrementId)
//    {
//        $args = array();
//        $args[0] = $invoiceIncrementId;
//        return $this->call( $sessionId, 'order_invoice.info', $args );
//    }
//
//    public function salesOrderInvoiceCreate( $sessionId, $orderIncrementId, $itemsQty, $comment=null, $email=null, $includeComment=null )
//    {
//        $args = array();
//        $args[0] = $orderIncrementId;
//        $args[1] = $itemsQty;
//        if (!is_null($comment)) {
//            $args[2] = $comment;
//        }
//        if (!is_null($email)) {
//            $args[3] = $email;
//        }
//        if (!is_null($includeComment)) {
//            $args[4] = $includeComment;
//        }
//        return $this->call( $sessionId, 'order_invoice.create', $args );
//    }
//
//    public function salesOrderInvoiceAddComment ( $sessionId, $invoiceIncrementId, $comment, $email=null, $includeComment=null)
//    {
//        $args = array();
//        $args[0] = $invoiceIncrementId;
//        $args[1] = $comment;
//        if (!is_null($email)) {
//            $args[2] = $email;
//        }
//        if (!is_null($includeComment)) {
//            $args[3] = $includeComment;
//        }
//        return $this->call( $sessionId, 'order_invoice.addComment', $args );
//    }
//
//    public function salesOrderInvoiceCapture( $sessionId, $invoiceIncrementId )
//    {
//        $args = array();
//        $args[0] = $invoiceIncrementId;
//        return $this->call( $sessionId, 'order_invoice.capture', $args );
//    }
//
//    public function salesOrderInvoiceVoid( $sessionId, $invoiceIncrementId )
//    {
//        $args = array();
//        $args[0] = $invoiceIncrementId;
//        return $this->call( $sessionId, 'order_invoice.void', $args );
//    }
//
//    public function salesOrderInvoiceCancel( $sessionId, $invoiceIncrementId )
//    {
//        $args = array();
//        $args[0] = $invoiceIncrementId;
//        return $this->call( $sessionId, 'order_invoice.cancel', $args );
//    }
//
//    public function catalogInventoryStockItemList( $sessionId, $products )
//    {
//        $args = array();
//        $args[0] = $products;
//        return $this->call( $sessionId, 'product_stock.list', $args );
//    }
//
//    public function catalogInventoryStockItemUpdate( $sessionId, $product, $data )
//    {
//        $args = array();
//        $args[0] = $product;
//        $dataArray = array();
//        foreach( $data as $k=>$v )  {
//            $dataArray[$k] = $v;
//        }
//        $args[1] = $dataArray;
//        return $this->call( $sessionId, 'product_stock.update', $args );
//    }
}