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
 * Block that renders Custom tab
 *
 * @method Mage_Core_Model_Theme getTheme()
 * @method setTheme($theme)
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 */
class Mage_DesignEditor_Block_Adminhtml_Editor_Tools_Code_Custom extends Mage_Backend_Block_Widget_Form
{
    /**
     * Upload file element html id
     */
    const FILE_ELEMENT_NAME = 'css_file_uploader';

    /**
     * Magento config model
     *
     * @var Mage_Core_Model_Config
     */
    protected $_config;

    /**
     * @param Mage_Core_Controller_Request_Http $request
     * @param Mage_Core_Model_Layout $layout
     * @param Mage_Core_Model_Event_Manager $eventManager
     * @param Mage_Backend_Model_Url $urlBuilder
     * @param Mage_Core_Model_Translate $translator
     * @param Mage_Core_Model_Cache $cache
     * @param Mage_Core_Model_Design_Package $designPackage
     * @param Mage_Core_Model_Session $session
     * @param Mage_Core_Model_Store_Config $storeConfig
     * @param Mage_Core_Controller_Varien_Front $frontController
     * @param Mage_Core_Model_Factory_Helper $helperFactory
     * @param Mage_Core_Model_Dir $dirs
     * @param Mage_Core_Model_Logger $logger
     * @param Magento_Filesystem $filesystem
     * @param Mage_Core_Model_Config $config
     * @param array $data
     *
     * @SuppressWarnings(PHPMD.ExcessiveParameterList)
     */
    public function __construct(
        Mage_Core_Controller_Request_Http $request,
        Mage_Core_Model_Layout $layout,
        Mage_Core_Model_Event_Manager $eventManager,
        Mage_Backend_Model_Url $urlBuilder,
        Mage_Core_Model_Translate $translator,
        Mage_Core_Model_Cache $cache,
        Mage_Core_Model_Design_Package $designPackage,
        Mage_Core_Model_Session $session,
        Mage_Core_Model_Store_Config $storeConfig,
        Mage_Core_Controller_Varien_Front $frontController,
        Mage_Core_Model_Factory_Helper $helperFactory,
        Mage_Core_Model_Dir $dirs,
        Mage_Core_Model_Logger $logger,
        Magento_Filesystem $filesystem,
        Mage_Core_Model_Config $config,
        array $data = array()
    ) {
        parent::__construct($request, $layout, $eventManager, $urlBuilder, $translator, $cache, $designPackage,
            $session, $storeConfig, $frontController, $helperFactory, $dirs, $logger, $filesystem, $data
        );
        $this->_config = $config;
    }

    /**
     * Create a form element with necessary controls
     *
     * @return Mage_Theme_Block_Adminhtml_System_Design_Theme_Edit_Tab_Css
     */
    protected function _prepareForm()
    {
        $form = new Varien_Data_Form(array(
            'action'   => '#',
            'method'   => 'post'
        ));
        $this->setForm($form);
        $form->setUseContainer(true);

        $form->addType('css_file',
            $this->_config->getBlockClassName('Mage_DesignEditor_Block_Adminhtml_Editor_Form_Element_File')
        );

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
     * @param Mage_Core_Model_Theme $theme
     * @return string
     */
    public function getDownloadCustomCssUrl($theme)
    {
        return $this->getUrl('*/system_design_theme/downloadCustomCss', array('theme_id' => $theme->getThemeId()));
    }

    /**
     * Get url to save custom CSS file
     *
     * @param Mage_Core_Model_Theme $theme
     * @return string
     */
    public function getSaveCustomCssUrl($theme)
    {
        return $this->getUrl('*/system_design_editor_tools/saveCssContent', array('theme_id' => $theme->getThemeId()));
    }

    /**
     * Get theme custom css content
     *
     * @param Mage_Core_Model_Theme $theme
     * @return string
     */
    public function getCustomCssContent($theme)
    {
        /** @var $cssFile Mage_Core_Model_Theme_Files */
        $cssFile = $theme->getCustomizationData(Mage_Core_Model_Theme_Customization_Files_Css::TYPE)->getFirstItem();
        return $cssFile->getContent();
    }

    /**
     * Get custom CSS file name
     *
     * @return string
     */
    public function getCustomFileName()
    {
        return pathinfo(Mage_Core_Model_Theme_Customization_Files_Css::FILE_PATH, PATHINFO_BASENAME);
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
