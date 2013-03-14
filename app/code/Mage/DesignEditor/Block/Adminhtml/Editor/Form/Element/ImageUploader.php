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
 */
class Mage_DesignEditor_Block_Adminhtml_Editor_Form_Element_ImageUploader
    extends Mage_DesignEditor_Block_Adminhtml_Editor_Form_Element_Uploader
{
    /**
     * Control type
     */
    const CONTROL_TYPE = 'image-uploader';

    /**
     * Default MIME types to accept
     */
    protected $_acceptTypesDefault = 'image/*';

    /**
     * Ability to upload multiple files by default is disabled for backgrounds
     */
    protected $_multipleFiles = true;

    /**
     * Constructor helper
     */
    public function _construct()
    {
        parent::_construct();

        $this->setAccept($this->_acceptTypesDefault);
        $this->setMultiple($this->_multipleFiles);

        $this->addClass('element-' . self::CONTROL_TYPE);
    }
}
