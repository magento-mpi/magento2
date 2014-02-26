<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_GoogleShopping
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\GoogleShopping\Model\Source;

/**
 * Google Data Api authorization types Source
 *
 * @category   Magento
 * @package    Magento_GoogleShopping
 * @author     Magento Core Team <core@magentocommerce.com>
 */
class Authtype implements \Magento\Option\ArrayInterface
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
