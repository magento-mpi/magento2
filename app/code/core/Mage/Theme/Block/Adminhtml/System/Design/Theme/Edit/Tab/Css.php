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
     * Uploader service
     *
     * @var Mage_Theme_Model_Uploader_Service
     */
    protected $_uploaderService;

    /**
     * Theme custom css file
     *
     * @var Mage_Core_Model_Theme_Files
     */
    protected $_customCssFile;

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
     * @param Mage_Theme_Model_Uploader_Service $uploaderService
     * @param Magento_Filesystem $filesystem
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
        Mage_Theme_Model_Uploader_Service $uploaderService,
        Magento_Filesystem $filesystem,
        array $data = array()
    ) {
        parent::__construct($request, $layout, $eventManager, $urlBuilder, $translator, $cache, $designPackage,
            $session, $storeConfig, $frontController, $helperFactory, $filesystem, $data
        );
        $this->_objectManager = $objectManager;
        $this->_uploaderService = $uploaderService;
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

        $this->_customCssFile = $this->_getCurrentTheme()
            ->getCustomizationData(Mage_Core_Model_Theme_Customization_Files_Css::TYPE)->getFirstItem();

        $this->_addCustomCssFieldset();

        $formData['custom_css_content'] = $this->_customCssFile->getContent();

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
        foreach ($this->_getGroupedFiles() as $groupName => $group) {
            $themeFieldset->addField('theme_css_view_' . $groupName, 'links', array(
                'label'       => $groupName,
                'title'       => $groupName,
                'name'        => 'links',
                'values'      => $group,
            ));
        }

        return $this;
    }

    /**
     * Prepare file items for output on page for download
     *
     * @param string $fileTitle
     * @param string $filePath
     * @return array
     */
    protected function _getThemeCss($fileTitle, $filePath)
    {
        $appPath = $this->_objectManager->get('Mage_Core_Model_Config')->getOptions()->getBaseDir();
        $shownFilePath = str_ireplace($appPath, '', $filePath);
        return array(
            'href'      => $this->getUrl('*/*/downloadCss', array(
                'theme_id' => $this->_getCurrentTheme()->getId(),
                'file'     => $this->_helperFactory->get('Mage_Theme_Helper_Data')->urlEncode($fileTitle))
            ),
            'label'     => $fileTitle,
            'title'     => $shownFilePath,
            'delimiter' => '<br />'
        );
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
        $this->_addElementTypes($themeFieldset);

        $themeFieldset->addField('css_file_uploader', 'css_file', array(
            'name'     => 'css_file_uploader',
            'label'    => $this->__('Select CSS File to Upload'),
            'title'    => $this->__('Select CSS File to Upload'),
            'accept'   => 'text/css',
            'note'     => $this->_getUploadCssFileNote()
        ));

        $themeFieldset->addField('css_uploader_button', 'button', array(
            'name'     => 'css_uploader_button',
            'value'    => $this->__('Upload CSS File'),
            'disabled' => 'disabled',
        ));

        $downloadButtonConfig = array(
            'name'  => 'css_download_button',
            'value' => $this->__('Download CSS File'),
            'onclick' => "setLocation('" . $this->getUrl('*/*/downloadCustomCss', array(
                'theme_id' => $this->_getCurrentTheme()->getId())) . "');"
        );
        if (!$this->_customCssFile->getContent()) {
            $downloadButtonConfig['disabled'] = 'disabled';
        }
        $themeFieldset->addField('css_download_button', 'button', $downloadButtonConfig);

        /** @var $imageButton Mage_Backend_Block_Widget_Button */
        $imageButton = $this->getLayout()->createBlock('Mage_Adminhtml_Block_Widget_Button')
            ->setData(array(
            'id'        => 'css_images_manager',
            'label'     => $this->__('Manage'),
            'class'     => 'button',
            'onclick'   => "MediabrowserUtility.openDialog('"
                . $this->getUrl('*/system_design_wysiwyg_files/index', array(
                    'target_element_id'                                => 'custom_css_content',
                    Mage_Theme_Helper_Storage::PARAM_NAME_THEME_ID     => $this->_getCurrentTheme()->getId(),
                    Mage_Theme_Helper_Storage::PARAM_NAME_CONTENT_TYPE => Mage_Theme_Model_Wysiwyg_Storage::TYPE_IMAGE
                ))
                . "', null, null,'"
                . $this->quoteEscape(
                    $this->__('Upload Images...'), true
                )
                . "');"
        ));

        $themeFieldset->addField('css_browse_image_button', 'note', array(
            'label' => $this->__("Images Assets"),
            'text'  => $imageButton->toHtml()
        ));

        /** @var $fontButton Mage_Backend_Block_Widget_Button */
        $fontButton = $this->getLayout()->createBlock('Mage_Adminhtml_Block_Widget_Button')
            ->setData(array(
            'id'        => 'css_fonts_manager',
            'label'     => $this->__('Manage'),
            'class'     => 'button',
            'onclick'   => "MediabrowserUtility.openDialog('"
                . $this->getUrl('*/system_design_wysiwyg_fonts/index', array(
                    'target_element_id'                                => 'custom_css_content',
                    Mage_Theme_Helper_Storage::PARAM_NAME_THEME_ID     => $this->_getCurrentTheme()->getId(),
                    Mage_Theme_Helper_Storage::PARAM_NAME_CONTENT_TYPE => Mage_Theme_Model_Wysiwyg_Storage::TYPE_FONT
                ))
                . "', null, null,'"
                . $this->quoteEscape(
                    $this->__('Upload fonts...'), true
                )
                . "');",
        ));

        $themeFieldset->addField('css_browse_font_button', 'note', array(
            'label' => $this->__("Fonts Assets"),
            'text'  => $fontButton->toHtml()
        ));

        $themeFieldset->addField('custom_css_content', 'textarea', array(
            'label'  => $this->__('Edit custom.css'),
            'title'  => $this->__('Edit custom.css'),
            'name'   => 'custom_css_content',
        ));

        return $this;
    }

    /**
     * Get note string for css file to Upload
     *
     * @return string
     */
    protected function _getUploadCssFileNote()
    {
        $messages = array(
            $this->__('Allowed file types *.css.'),
            $this->__('The file you upload will replace the existing custom.css file (shown below).')
        );
        $maxFileSize = $this->_objectManager->get('Magento_File_Size')->getMaxFileSizeInMb();
        if ($maxFileSize) {
            $messages[] = $this->__('Max file size to upload %sM', $maxFileSize);
        } else {
            $messages[] = $this->__('System doesn\'t allow to get file upload settings');
        }

        return implode('<br />', $messages);
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
        $linksElement = $this->_objectManager->get('Mage_Core_Model_Config')
            ->getBlockClassName('Mage_Theme_Block_Adminhtml_System_Design_Theme_Edit_Form_Element_Links');
        $fileElement = $this->_objectManager->get('Mage_Core_Model_Config')
            ->getBlockClassName('Mage_Theme_Block_Adminhtml_System_Design_Theme_Edit_Form_Element_File');
        return array('links' => $linksElement, 'css_file' => $fileElement);
    }

    /**
     * Get files by groups
     *
     * @return array
     */
    protected function _getGroupedFiles()
    {
        $options = $this->_objectManager->get('Mage_Core_Model_Config')->getOptions();
        $jsDir = $options->getJsDir();
        $codeDir = $options->getCodeDir();

        $groups = array();
        $themes = array();
        foreach ($this->getFiles() as $fileTitle => $filePath) {
            /** @var $theme Mage_Core_Model_Theme */
            list($group, $theme) = $this->_getGroup($filePath);
            if ($theme) {
                $themes[$theme->getThemeId()] = $theme;
            }

            if (!isset($groups[$group])) {
                $groups[$group] = array();
            }
            $groups[$group][] = $this->_getThemeCss($fileTitle, $filePath);
        }

        if (count($themes) > 1) {
            $themes = $this->_sortThemesByHierarchy($themes);
        }

        $order = array_merge(array($codeDir, $jsDir), array_map(function ($theme) {
            /** @var $theme Mage_Core_Model_Theme */
            return $theme->getThemeId();
        }, $themes));
        $groups = $this->_sortArrayByArray($groups, $order);

        $labels = $this->_getGroupLabels($themes);
        foreach ($groups as $key => $group) {
            usort($group, array($this, '_sortGroupFilesCallback'));
            $groups[$labels[$key]] = $group;
            unset($groups[$key]);
        }
        return $groups;
    }

    /**
     * Sort files inside group
     *
     * @param array $firstGroup
     * @param array $secondGroup
     * @return int
     */
    protected function _sortGroupFilesCallback($firstGroup, $secondGroup)
    {
        $hasContextFirst = strpos($firstGroup['label'], '::') !== false;
        $hasContextSecond = strpos($secondGroup['label'], '::') !== false;

        if ($hasContextFirst && $hasContextSecond) {
            $result = strcmp($firstGroup['label'], $secondGroup['label']);
        } elseif (!$hasContextFirst && !$hasContextSecond) {
            $result = strcmp($firstGroup['label'], $secondGroup['label']);
        } elseif ($hasContextFirst) {
            //case when first item has module context and second item doesn't
            $result = 1;
        } else {
            //case when second item has module context and first item doesn't
            $result = -1;
        }
        return $result;
    }

    /**
     * Get group by filename
     *
     * @param string $filename
     * @return array
     * @throws Mage_Core_Exception
     */
    protected function _getGroup($filename)
    {
        $options = $this->_objectManager->get('Mage_Core_Model_Config')->getOptions();
        $designDir = $options->getDesignDir();
        $jsDir = $options->getJsDir();
        $codeDir = $options->getCodeDir();

        $group = null;
        $theme = null;
        if (substr($filename, 0, strlen($designDir)) == $designDir) {
            $theme = $this->_getThemeByFilename(substr($filename, strlen($designDir)));
            $group = $theme->getThemeId();
        } elseif (substr($filename, 0, strlen($jsDir)) == $jsDir) {
            $group = $jsDir;
        } elseif (substr($filename, 0, strlen($codeDir)) == $codeDir) {
            $group = $codeDir;
        } else {
            Mage::throwException($this->__('Invalid view file directory "%s"', $filename));
        }

        return array($group, $theme);
    }

    /**
     * Sort themes according to their hierarchy
     *
     * @param array $themes
     * @return array
     */
    protected function _sortThemesByHierarchy($themes)
    {
        uasort($themes, array($this, '_sortThemesByHierarchyCallback'));
        return $themes;
    }

    /**
     * Sort themes by hierarchy callback
     *
     * @param Mage_Core_Model_Theme $firstTheme
     * @param Mage_Core_Model_Theme $secondTheme
     * @return int
     */
    protected function _sortThemesByHierarchyCallback($firstTheme, $secondTheme)
    {
        $parentTheme = $firstTheme->getParentTheme();
        while ($parentTheme) {
            if ($parentTheme->getId() == $secondTheme->getId()) {
                return -1;
            }
            $parentTheme = $parentTheme->getParentTheme();
        }
        return 1;
    }

    /**
     * Get theme object that contains given file
     *
     * @param string $filename
     * @return Mage_Core_Model_Theme
     * @throws InvalidArgumentException
     */
    protected function _getThemeByFilename($filename)
    {
        $area = strtok($filename, DIRECTORY_SEPARATOR);
        $package = strtok(DIRECTORY_SEPARATOR);
        $theme = strtok(DIRECTORY_SEPARATOR);

        if ($area === false || $package === false || $theme === false) {
            throw new InvalidArgumentException('Theme path does not recognized');
        }
        /** @var $collection Mage_Core_Model_Resource_Theme_Collection */
        $collection = $this->_objectManager->create('Mage_Core_Model_Resource_Theme_Collection');
        return $collection->getThemeByFullPath($area . '/' . $package . '/' . $theme);
    }

    /**
     * Get group labels
     *
     * @param array $themes
     * @return array
     */
    protected function _getGroupLabels($themes)
    {
        $labels = array(
            $this->_objectManager->get('Mage_Core_Model_Config')->getOptions()->getJsDir()
                => $this->__('Library files'),
            $this->_objectManager->get('Mage_Core_Model_Config')->getOptions()->getCodeDir()
                => $this->__('Framework files')
        );
        foreach ($themes as $theme) {
            /** @var $theme Mage_Core_Model_Theme */
            $labels[$theme->getThemeId()] = $this->__('"%s" Theme files', $theme->getThemeTitle());
        }
        return $labels;
    }

    /**
     * Sort one associative array according to another array
     *
     * $groups = array(
     *     b => item2,
     *     a => item1,
     *     c => item3,
     * );
     * $order = array(a,b,c);
     * result: array(
     *     a => item1,
     *     b => item2,
     *     c => item3,
     * )
     *
     * @param array $groups
     * @param array $order
     * @return array
     */
    protected function _sortArrayByArray($groups, $order)
    {
        $ordered = array();
        foreach ($order as $key) {
            if (array_key_exists($key, $groups)) {
                $ordered[$key] = $groups[$key];
                unset($groups[$key]);
            }
        }
        return $ordered + $groups;
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
