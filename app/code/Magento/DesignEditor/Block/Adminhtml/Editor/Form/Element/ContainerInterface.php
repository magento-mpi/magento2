<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_DesignEditor
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Interface for form element that can contain other elements
 */
interface Magento_DesignEditor_Block_Adminhtml_Editor_Form_Element_ContainerInterface
{
    /**
     * Add fields to composite composite element
     *
     * @param string $elementId
     * @param string $type
     * @param array $config
     * @param boolean $after
     * @param boolean $isAdvanced
     * @return Magento_Data_Form_Element_Abstract
     */
    public function addField($elementId, $type, $config, $after = false, $isAdvanced = false);
}
