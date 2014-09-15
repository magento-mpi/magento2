<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */


/**
 * Send to a Friend Limit sending by Source
 *
 * @author      Magento Core Team <core@magentocommerce.com>
 */
namespace Magento\Sendfriend\Model\Source;

class Checktype implements \Magento\Framework\Option\ArrayInterface
{
    /**
     * Retrieve Check Type Option array
     *
     * @return array
     */
    public function toOptionArray()
    {
        return array(
            array('value' => \Magento\Sendfriend\Helper\Data::CHECK_IP, 'label' => __('IP Address')),
            array('value' => \Magento\Sendfriend\Helper\Data::CHECK_COOKIE, 'label' => __('Cookie (unsafe)'))
        );
    }
}
