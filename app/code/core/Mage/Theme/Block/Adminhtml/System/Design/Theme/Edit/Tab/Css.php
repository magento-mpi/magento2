<?php
/**
 * {license_notice}
 *
 * @category    Mage
 * @package     Mage_Theme
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Theme form, Css editor tab
 *
 * @method Mage_Theme_Block_Adminhtml_System_Design_Theme_Edit_Tab_Css setFiles(array $files)
 * @method array getFiles()
 *
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 */
class Mage_Theme_Block_Adminhtml_System_Design_Theme_Edit_Tab_Css
    extends Mage_Backend_Block_Widget_Form
    implements Mage_Backend_Block_Widget_Tab_Interface
{
    /**
     * @var Magento_ObjectManager
     */
    protected $_objectManager;

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
     * @param Magento_ObjectManager $objectManager
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
        Magento_ObjectManager $objectManager,
        array $data = array()
    ) {
        parent::__construct($request, $layout, $eventManager, $urlBuilder, $translator, $cache, $designPackage,
            $session, $storeConfig, $frontController, $helperFactory, $data
        );
        $this->_objectManager = $objectManager;
    }

    /**
     * Create a form element with necessary controls
     *
     * @return Mage_Theme_Block_Adminhtml_System_Design_Theme_Edit_Tab_Css
     */
    protected function _prepareForm()
    {
        $form = new Varien_Data_Form();
        $this->setForm($form);
        $this->_addThemeCssFieldset();
        $this->_addCustomCssFieldset();

        $formData['custom_css_content'] = $this->_getCurrentTheme()->getCssFile()->getContent();
        /** @var $session Mage_Backend_Model_Session */
        $session = $this->_objectManager->get('Mage_Backend_Model_Session');
        $cssFileContent = $session->getThemeCustomCssData();
        if ($cssFileContent) {
            $formData['custom_css_content'] = $cssFileContent;
            $session->unsThemeCustomCssData();
        }
        $form->addValues($formData);
        return $this;
    }

    /**
     * Set theme css fieldset
     *
     * @return Mage_Theme_Block_Adminhtml_System_Design_Theme_Edit_Tab_Css
     */
    protected function _addThemeCssFieldset()
    {
        $form = $this->getForm();
        $themeFieldset = $form->addFieldset('theme_css', array(
            'legend' => $this->__('Theme CSS'),
            'class'  => 'fieldset-wide'
        ));
        $this->_addElementTypes($themeFieldset);
        $themeFieldset->addField('theme_css_view', 'links', array(
            'label'       => $this->__('View theme CSS'),
            'title'       => $this->__('View theme CSS'),
            'name'        => 'links',
            'values'      => $this->_getThemeCssList(),
        ));
        return $this;
    }

    /**
     * Prepare file items for output on page for download
     *
     * @return array
     */
    protected function _getThemeCssList()
    {
        $files = $this->getFiles();
        $data = array();
        foreach ($files as $title => $url) {
            $data[] = array(
                'href'      => $url,
                'label'     => $title,
                'target'    => '_blank',
                'delimiter' => '<br />',
            );
        }
        return $data;
    }

    /**
     * Set custom css fieldset
     *
     * @return Mage_Theme_Block_Adminhtml_System_Design_Theme_Edit_Tab_Css
     */
    protected function _addCustomCssFieldset()
    {
        $form = $this->getForm();
        $themeFieldset = $form->addFieldset('custom_css', array(
            'legend' => $this->__('Custom CSS'),
            'class'  => 'fieldset-wide'
        ));

        $themeFieldset->addField('css_file_uploader', 'file', array(
            'name'     => 'css_file_uploader',
            'label'    => $this->__('Select CSS File to Upload'),
            'title'    => $this->__('Select CSS File to Upload'),
        ));

        $themeFieldset->addField('css-uploader-button', 'button', array(
            'name'     => 'css-uploader-button',
            'value'    => $this->__('Upload CSS File'),
            'disabled' => 'disabled',
        ));

        $themeFieldset->addField('custom_css_content', 'textarea', array(
            'label'  => $this->__('Edit custom.css'),
            'title'  => $this->__('Edit custom.css'),
            'name'   => 'custom_css_content',
            'values' => 'some text'
        ));

        return $this;
    }

    /**
     * Get current theme
     *
     * @return Mage_Core_Model_Theme
     */
    protected function _getCurrentTheme()
    {
        return Mage::registry('current_theme');
    }

    /**
     * Set additional form field type for theme preview image
     *
     * @return array
     */
    protected function _getAdditionalElementTypes()
    {
        $element = Mage::getConfig()
            ->getBlockClassName('Mage_Theme_Block_Adminhtml_System_Design_Theme_Edit_Form_Element_Links');
        return array('links' => $element);
    }

    /**
     * Return Tab label
     *
     * @return string
     */
    public function getTabLabel()
    {
        return $this->__('CSS Editor');
    }

    /**
     * Return Tab title
     *
     * @return string
     */
    public function getTabTitle()
    {
        return $this->getTabLabel();
    }

    /**
     * Can show tab in tabs
     *
     * @return boolean
     */
    public function canShowTab()
    {
        return $this->_getCurrentTheme()->isVirtual() && $this->_getCurrentTheme()->getId();
    }

    /**
     * Tab is hidden
     *
     * @return boolean
     */
    public function isHidden()
    {
        return false;
    }
}
