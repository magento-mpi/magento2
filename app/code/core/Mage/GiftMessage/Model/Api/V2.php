<?php
/**
 * {license_notice}
 *
 * @category    Mage
 * @package     Mage_GiftMessage
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * GiftMessage api
 *
 * @category   Mage
 * @package    Mage_GiftMessage
 * @author     Magento Core Team <core@magentocommerce.com>
 */
class Mage_GiftMessage_Model_Api_V2 extends Mage_GiftMessage_Model_Api
{

    /**
     * Return an Array of Object attributes.
     *
     * @param Mixed $data
     * @return Array
     */
    protected function _prepareData($data){
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
            $arr = $this->_prepareData($arr);
            return parent::_prepareData($arr);
        }
        if (is_array($data)) {
            foreach ($data as $key => $value) {
                if (is_object($value) || is_array($value)) {
                    $data[$key] = $this->_prepareData($value);
                } else {
                    $data[$key] = $value;
                }
            }
            return parent::_prepareData($data);
        }
        return $data;
    }

    /**
     * Raise event for setting a giftMessage.
     *
     * @param String $entityId
     * @param Mage_Core_Controller_Request_Http $request
     * @param Mage_Sales_Model_Quote $quote
     * @return stdClass
     */
    protected function _setGiftMessage($entityId, $request, $quote) {
        $response = new stdClass();
        $response->entityId = $entityId;

        /**
         * Below code will catch exceptions only in DeveloperMode
         * @see Mage_Core_Model_App::_callObserverMethod($object, $method, $observer)
         * And result of Mage::dispatchEvent will always return an Object of Mage_Core_Model_App.
         */
        try {
            Mage::dispatchEvent('checkout_controller_onepage_save_shipping_method', array('request'=>$request, 'quote'=>$quote));
            $response->result = true;
            $response->error = '';
        } catch (Exception $e) {
            $response->result = false;
            $response->error = $e->getMessage();
        }
        return $response;
    }

}
?>
