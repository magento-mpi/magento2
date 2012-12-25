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
class Saas_PrintedTemplate_Model_Source_Variables
{
    /**
     * Prepares option array of printed template variables
     *
     * @param int $templateType
     * @return array
     */
    public function toOptionArray($templateType = null)
    {
        $optionArray = array();
        $variables = Mage::getSingleton('Saas_PrintedTemplate_Model_Config')->getVariablesArray($templateType);
        foreach ($variables as $entity => $options) {
            if (!isset($options['fields'])) {
                continue;
            }
            $optionArrayVariables = array();
            foreach ($options['fields'] as $fieldName => $variable) {
                $optionArrayVariables[] = array(
                    'value' => '{{var ' . $entity . '.' . $fieldName . '}}',
                    'label' => Mage::helper('Saas_PrintedTemplate_Helper_Data')->__($variable['label'])
                );
            }
            $label = isset($options['label']) ? $options['label'] : $entity;
            $optionArray[] = array(
                'label' => Mage::helper('Saas_PrintedTemplate_Helper_Data')->__($label),
                'value' => $optionArrayVariables
            );
        }
        return $optionArray;
    }
}
