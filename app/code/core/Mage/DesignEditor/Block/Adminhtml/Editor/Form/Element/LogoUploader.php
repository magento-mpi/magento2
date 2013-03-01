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
 * Form element renderer to display logo uploader element for VDE
 *
 * @SuppressWarnings(PHPMD.DepthOfInheritance)
 */
class Mage_DesignEditor_Block_Adminhtml_Editor_Form_Element_LogoUploader
    extends Mage_DesignEditor_Block_Adminhtml_Editor_Form_Element_ImageUploader
{
    /**
     * Control type
     */
    const CONTROL_TYPE = 'logo-uploader';

    /**
     * Ability to upload multiple files by default is disabled for logo
     */
    protected $_multipleFiles = false;
}
