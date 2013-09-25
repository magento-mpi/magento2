<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Paypal
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Source model for url method: GET/POST
 */
class Magento_Paypal_Model_System_Config_Source_UrlMethod implements Magento_Core_Model_Option_ArrayInterface
{
    /**
     * Options getter
     *
     * @return array
     */
    public function toOptionArray()
    {
        return array(
            array('value' => 'GET', 'label' => 'GET'),
            array('value' => 'POST', 'label' => 'POST'),
        );
    }
}
