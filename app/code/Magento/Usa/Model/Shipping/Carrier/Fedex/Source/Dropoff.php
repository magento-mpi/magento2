<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Usa
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Fedex dropoff source implementation
 *
 * @category   Magento
 * @package    Magento_Usa
 * @author     Magento Core Team <core@magentocommerce.com>
 */
namespace Magento\Usa\Model\Shipping\Carrier\Fedex\Source;

class Dropoff
{
    public function toOptionArray()
    {
        $fedex = \Mage::getSingleton('Magento\Usa\Model\Shipping\Carrier\Fedex');
        $arr = array();
        foreach ($fedex->getCode('dropoff') as $k => $v) {
            $arr[] = array('value' => $k, 'label' => $v);
        }
        return $arr;
    }
}
