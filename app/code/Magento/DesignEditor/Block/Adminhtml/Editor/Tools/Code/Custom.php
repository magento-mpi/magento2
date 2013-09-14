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
 * Block that renders Custom tab
 */
namespace Magento\DesignEditor\Block\Adminhtml\Editor\Tools\Code;

class Custom extends \Magento\Backend\Block\Widget\Form
{
    /**
     * Upload file element html id
     */
    const FILE_ELEMENT_NAME = 'css_file_uploader';

    /**
     * @var \Magento\DesignEditor\Model\Theme\Context
     */
    protected $_themeContext;

    /**
     * @param \Magento\Backend\Block\Template\Context $context
     * @param \Magento\DesignEditor\Model\Theme\Context $themeContext
     * @param array $data
     */
    public function __construct(
        \Magento\Backend\Block\Template\Context $context,
        \Magento\DesignEditor\Model\Theme\Context $themeContext,
        array $data = array()
    ) {
        parent::__construct($context, $data);
        $this->_themeContext = $themeContext;
    }


    /**
     * Create a form element with necessary controls
     *
     * @return \Magento\Theme\Block\Adminhtml\System\Design\Theme\Edit\Tab\Css
     */
    protected function _prepareForm()
    {
        $form = new \Magento\Data\Form(array(
            'action'   => '#',
            'method'   => 'post'
        ));
        $this->setForm($form);
        $form->setUseContainer(true);

        $form->addType('css_file', 'Magento\DesignEditor\Block\Adminhtml\Editor\Form\Element\Uploader');

        $form->addField($this->getFileElementName(), 'css_file', array(
            'name'     => $this->getFileElementName(),
            'accept'   => 'text/css',
            'no_span'  => true
        ));

        parent::_prepareForm();
        return $this;
    }

    /**
     * Get url to download custom CSS file
     *
     * @return string
     */
    public function getDownloadCustomCssUrl()
    {
        return $this->getUrl('*/system_design_theme/downloadCustomCss',
            array('theme_id' => $this->_themeContext->getEditableTheme()->getId()));
    }

    /**
     * Get url to upload custom CSS file
     *
     * @return string
     */
    public function getUploadUrl()
    {
        return $this->getUrl('*/system_design_editor_tools/upload',
            array('theme_id' => $this->_themeContext->getEditableTheme()->getId()));
    }

    /**
     * Get url to save custom CSS file
     *
     * @return string
     */
    public function getSaveCustomCssUrl()
    {
        return $this->getUrl('*/system_design_editor_tools/saveCssContent',
            array('theme_id' => $this->_themeContext->getEditableTheme()->getId()));
    }

    /**
     * Get theme custom css content
     *
     * @param string $targetElementId
     * @param string $contentType
     * @return string
     */
    public function getMediaBrowserUrl($targetElementId, $contentType)
    {
        return $this->getUrl('*/system_design_editor_files/index', array(
            'target_element_id'                           => $targetElementId,
            \Magento\Theme\Helper\Storage::PARAM_THEME_ID     => $this->_themeContext->getEditableTheme()->getId(),
            \Magento\Theme\Helper\Storage::PARAM_CONTENT_TYPE => $contentType
        ));
    }

    /**
     * Get theme file (with custom CSS)
     *
     * @param \Magento\Core\Model\Theme $theme
     * @return \Magento\Core\Model\Theme\FileInterface|null
     */
    protected function _getCustomCss($theme)
    {
        $files = $theme->getCustomization()->getFilesByType(
            \Magento\Theme\Model\Theme\Customization\File\CustomCss::TYPE
        );
        return reset($files);
    }

    /**
     * Get theme custom CSS content
     *
     * @return null|string
     */
    public function getCustomCssContent()
    {
        $customCss = $this->_getCustomCss($this->_themeContext->getStagingTheme());
        return $customCss ? $customCss->getContent() : null;
    }

    /**
     * Get custom CSS file name
     *
     * @return string|null
     */
    public function getCustomFileName()
    {
        $customCss = $this->_getCustomCss($this->_themeContext->getStagingTheme());
        return $customCss ? $customCss->getFileName() : null;
    }

    /**
     * Get file element name
     *
     * @return string
     */
    public function getFileElementName()
    {
        return self::FILE_ELEMENT_NAME;
    }
}
