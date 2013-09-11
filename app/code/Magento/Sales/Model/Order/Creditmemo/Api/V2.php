<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Sales
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Credit memo API
 *
 * @category   Magento
 * @package    Magento_Sales
 * @author     Magento Core Team <core@magentocommerce.com>
 */
namespace Magento\Sales\Model\Order\Creditmemo\Api;

class V2 extends \Magento\Sales\Model\Order\Creditmemo\Api
{
    /**
     * Prepare data
     *
     * @param null|object $data
     * @return array
     */
    protected function _prepareCreateData($data)
    {
        // convert data object to array, if it's null turn it into empty array
        $data = (isset($data) and is_object($data)) ? get_object_vars($data) : array();
        // convert qtys object to array
        if (isset($data['qtys']) && count($data['qtys'])) {
            $qtysArray = array();
            foreach ($data['qtys'] as &$item) {
                if (isset($item->order_item_id) && isset($item->qty)) {
                    $qtysArray[$item->order_item_id] = $item->qty;
                }
            }
            $data['qtys'] = $qtysArray;
        }
        return $data;
    }
}
