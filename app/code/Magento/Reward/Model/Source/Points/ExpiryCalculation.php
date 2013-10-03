<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Reward
 * @copyright   {copyright}
 * @license     {license_link}
 */


/**
 * Source model for list of Expiry Calculation algorythms
 */
namespace Magento\Reward\Model\Source\Points;

class ExpiryCalculation implements \Magento\Core\Model\Option\ArrayInterface
{
    public function toOptionArray()
    {
        return array(
            array('value' => 'static', 'label' => __('Static')),
            array('value' => 'dynamic', 'label' => __('Dynamic')),
        );
    }
}
