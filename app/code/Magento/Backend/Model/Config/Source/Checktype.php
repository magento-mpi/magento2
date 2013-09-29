<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Backend
 * @copyright   {copyright}
 * @license     {license_link}
 */


/**
 * Send to a Friend Limit sending by Source
 *
 * @category    Magento
 * @package     Magento_Backend
 * @author      Magento Core Team <core@magentocommerce.com>
 */
namespace Magento\Backend\Model\Config\Source;

class Checktype implements \Magento\Core\Model\Option\ArrayInterface
{
    /**
     * Retrieve Check Type Option array
     *
     * @return array
     */
    public function toOptionArray()
    {
        return array(
            array(
                'value' => \Magento\Sendfriend\Helper\Data::CHECK_IP,
                'label' => __('IP Address')
            ),
            array(
                'value' => \Magento\Sendfriend\Helper\Data::CHECK_COOKIE,
                'label' => __('Cookie (unsafe)')
            ),
        );
    }
}
