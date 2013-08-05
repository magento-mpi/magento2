<?php
/**
 * {license_notice}
 *
 * @category    Saas
 * @package     Saas_PrintedTemplate
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Custom variables for printed templates
 *
 * @category    Saas
 * @package     Saas_PrintedTemplate
 * @subpackage  Models
 */
class Saas_PrintedTemplate_Model_Source_StoreVariables extends Mage_Core_Model_Source_Email_Variables
{
    /**
     * Adds store logo URL and address to Store Contact Information group of variables
     * @see Mage_Core_Model_Source_Email_Variables::toOptionArray()
     */
    public function toOptionArray($withGroup = false)
    {
        $options = parent::toOptionArray($withGroup);
        $logoOption = array(
            'value' => '{{var config.store_logo_url}}',
            'label' => __('Store Logo URL'.
                    ' (Configuration -> Sales -> Invoice and Packing Slip Design -> Logo for PDF Print-outs)'),
        );
        $addressOption = array(
            'value' => '{{var config.store_address}}',
            'label' => __('Store Address '.
                    '(Configuration -> Sales -> Invoice and Packing Slip Design -> Address)'),
        );

        if (isset($options['value'])) {
            $options['value'][] = $logoOption;
            $options['value'][] = $addressOption;
        } else {
            $options[] = $logoOption;
            $options[] = $addressOption;
        }

        return $options;
    }
}
