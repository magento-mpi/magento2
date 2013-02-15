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
    extends Mage_DesignEditor_Block_Adminhtml_Editor_Form_Element_File
{
    /**
     * Default MIME types to accept
     */
    protected $_acceptTypesDefault = 'image/*';

    /**
     * Ability to upload mutiple files by default is disabed for backgrounds
     */
    protected $_multipleFilesDefault = false;

    /**
     * Constructor helper
     */
    public function _construct()
    {
        parent::_construct();

        $this->setAccept($this->_acceptTypesDefault);
        $this->setMultiple($this->_multipleFilesDefault);
    }
}

