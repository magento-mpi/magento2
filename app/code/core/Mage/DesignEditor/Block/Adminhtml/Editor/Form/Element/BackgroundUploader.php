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
 * Form element renderer to display background uploader element for VDE
 */
class Mage_DesignEditor_Block_Adminhtml_Editor_Form_Element_BackgroundUploader
    extends Mage_DesignEditor_Block_Adminhtml_Editor_Form_Element_ImageUploader
{
    const CONTROL_TYPE = 'background-uploader';

    /**
     * Ability to upload multiple files by default is disabled for backgrounds
     */
    protected $_multipleFilesDefault = false;

    /**
     * Constructor helper
     */
    public function _construct()
    {
        parent::_construct();
    }
}

