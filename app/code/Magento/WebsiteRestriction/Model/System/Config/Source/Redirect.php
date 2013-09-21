<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_WebsiteRestriction
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Sys config source model for private sales redirect modes
 *
 */
class Magento_WebsiteRestriction_Model_System_Config_Source_Redirect extends Magento_Object
    implements Magento_Core_Model_Option_ArrayInterface
{
    /**
     * Get options for select
     *
     * @return array
     */
    public function toOptionArray()
    {
        return array(
            array(
                'value' => Magento_WebsiteRestriction_Model_Mode::HTTP_302_LOGIN,
                'label' => __('To login form (302 Found)'),
            ),
            array(
                'value' => Magento_WebsiteRestriction_Model_Mode::HTTP_302_LANDING,
                'label' => __('To landing page (302 Found)'),
            ),
        );
    }
}
