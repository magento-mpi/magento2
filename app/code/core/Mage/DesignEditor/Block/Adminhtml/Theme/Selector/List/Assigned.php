<?php
/**
 * {license_notice}
 *
 * @category    Mage
 * @package     Mage_DesignEditor
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Assigned theme list
 */
class Mage_DesignEditor_Block_Adminhtml_Theme_Selector_List_Assigned
    extends Mage_DesignEditor_Block_Adminhtml_Theme_Selector_List_Abstract
{
    /**
     * Get list title
     *
     * @return string
     */
    public function getTabTitle()
    {
        return $this->__('Themes Assigned to Storeviews');
    }
}
