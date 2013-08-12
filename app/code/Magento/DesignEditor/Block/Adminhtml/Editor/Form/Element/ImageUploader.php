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
 * Form element renderer to display logo uploader element for VDE
 */
class Magento_DesignEditor_Block_Adminhtml_Editor_Form_Element_ImageUploader
    extends Magento_DesignEditor_Block_Adminhtml_Editor_Form_Element_Uploader
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
     * Constructor helper
     */
    public function _construct()
    {
        parent::_construct();
        $this->setAccept($this->_acceptTypesDefault);
        $this->addClass('element-' . self::CONTROL_TYPE);
    }
}
