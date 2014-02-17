<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Usa
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Usa\Model\Shipping\Carrier\Fedex\Source;

/**
 * Fedex freemethod source implementation
 *
 * @category   Magento
 * @package    Magento_Usa
 * @author     Magento Core Team <core@magentocommerce.com>
 */
class Freemethod
    extends \Magento\Usa\Model\Shipping\Carrier\Fedex\Source\Method
{
    /**
     * {@inheritdoc}
     */
    public function toOptionArray()
    {
        $arr = parent::toOptionArray();
        array_unshift($arr, array('value' => '', 'label' => __('None')));
        return $arr;
    }
}
