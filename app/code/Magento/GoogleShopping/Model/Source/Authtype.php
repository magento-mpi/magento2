<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\GoogleShopping\Model\Source;

/**
 * Google Data Api authorization types Source
 *
 * @author     Magento Core Team <core@magentocommerce.com>
 */
class Authtype implements \Magento\Framework\Option\ArrayInterface
{
    /**
     * Retrieve option array with authentification types
     *
     * @return array
     */
    public function toOptionArray()
    {
        return array(
            array('value' => 'authsub', 'label' => __('AuthSub')),
            array('value' => 'clientlogin', 'label' => __('ClientLogin'))
        );
    }
}
