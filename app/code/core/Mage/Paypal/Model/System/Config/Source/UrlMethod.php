<?php
/**
 * {license_notice}
 *
 * @category    Mage
 * @package     Mage_Paypal
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Source model for url method: GET/POST
 */
class Mage_Paypal_Model_System_Config_Source_UrlMethod
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
