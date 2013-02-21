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
class Mage_DesignEditor_Block_Adminhtml_Editor_Form_Element_LogoUploader
    extends Mage_DesignEditor_Block_Adminhtml_Editor_Form_Element_File
{
    const CONTROL_TYPE = 'logo-uploader';

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

        $this->addFields();

        $this->setAccept($this->_acceptTypesDefault);
        $this->setMultiple($this->_multipleFilesDefault);

        $this->addClass('element-' . self::CONTROL_TYPE);
    }

    /**
     * Add form elements
     */
    public function addFields()
    {
        $components = $this->getComponents();
        $uploaderData = $components['header-background:background-uploader']['components']['header-background:image-uploader'];
        $checkboxData = $components['header-background:background-uploader']['components']['header-background:tile'];
        $colorData = $components['header-background:color-picker'];

        $colorTitle = sprintf("%s {%s: %s}",
            $colorData['selector'],
            $colorData['attribute'],
            $colorData['value']
        );
        $colorHtmlId = uniqid('color-picker-');
        $this->addField(uniqid('background-color-picker-'), 'color-picker', array(
            'name'  => $colorHtmlId,
            'value' => $colorData['value'],
            'title' => $colorTitle,
            'label' => null,
        ));

        $uploaderTitle = sprintf('%s {%s: url(%s)}',
            $uploaderData['selector'],
            $uploaderData['attribute'],
            $uploaderData['value']
        );
        $uploaderHtmlId = uniqid('background-uploader-');
        $uploaderConfig = array(
            'name'     => $uploaderHtmlId,
            'title'    => $uploaderTitle,
            'label'    => null,
            //'onclick'  => "return confirm('Are you sure?');",
            //'values'   => $files,
        );
        $this->addField($uploaderHtmlId, 'background-uploader', $uploaderConfig);


    }
}
