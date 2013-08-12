<?php
/**
 * {license_notice}
 *
 * @category    Enterprise
 * @package     Enterprise_WebsiteRestriction
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Sys config source model for stub page statuses
 *
 */
class Enterprise_WebsiteRestriction_Model_System_Config_Source_Http
extends Magento_Object
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
                'value' => Enterprise_WebsiteRestriction_Model_Mode::HTTP_503,
                'label' => __('503 Service Unavailable'),
            ),
            array(
                'value' => Enterprise_WebsiteRestriction_Model_Mode::HTTP_200,
                'label' => __('200 OK'),
            ),
        );
    }
}
