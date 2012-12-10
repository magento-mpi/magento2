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
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 */
class Mage_DesignEditor_Block_Adminhtml_Theme_Preview extends Mage_Core_Block_Template
{
    /**
     * Theme parameter name
     */
    const PARAM_THEME_ID = 'theme_id';

    /**
     * Preview type parameter name
     */
    const PARAM_PREVIEW = 'preview_type';

    /**
     * Theme factory
     *
     * @var Mage_Core_Model_Theme_Factory
     */
    protected $_themeFactory;

    /**
     * Current theme used for preview
     *
     * @var Mage_Core_Model_Theme
     */
    protected $_theme;

    /**
     * Preview Factory
     *
     * @var Mage_DesignEditor_Model_Theme_PreviewFactory
     */
    protected $_previewFactory;

    /**
     * Initialize dependencies
     *
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
     * @param Mage_Core_Model_Theme_Factory $themeFactory
     * @param Mage_DesignEditor_Model_Theme_PreviewFactory $previewFactory
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
        Mage_Core_Model_Theme_Factory $themeFactory,
        Mage_DesignEditor_Model_Theme_PreviewFactory $previewFactory,
        array $data = array()
    ) {
        parent::__construct($request, $layout, $eventManager, $urlBuilder, $translator, $cache, $designPackage,
            $session, $storeConfig, $frontController, $helperFactory, $data);
        $this->_themeFactory = $themeFactory;
        $this->_previewFactory = $previewFactory;
    }

    /**
     * Get current theme for preview
     *
     * @return Mage_Core_Model_Theme
     * @throws Magento_Exception
     */
    public function getTheme()
    {
        if ($this->_theme) {
            return $this->_theme;
        }

        $themeId = (int)$this->getRequest()->getParam(self::PARAM_THEME_ID);
        if (!$themeId) {
            throw new Magento_Exception($this->__('You need to set theme for preview'));
        }
        $this->_theme = $this->_themeFactory->create()->load($themeId);
        return $this->_theme;
    }

    /**
     * Get current preview type
     *
     * @return string
     */
    public function getPreviewType()
    {
        $previewType = $this->getRequest()->getParam(self::PARAM_PREVIEW);
        return empty($previewType) ? Mage_DesignEditor_Model_Theme_PreviewFactory::TYPE_DEFAULT : $previewType;
    }

    /**
     * Return preview store url
     *
     * @return string
     */
    public function getPreviewUrl()
    {
        return $this->_previewFactory->create($this->getPreviewType())
            ->setTheme($this->getTheme())
            ->getPreviewUrl();
    }

    /**
     * Get assign to storeview button
     *
     * @return string
     */
    public function getAssignButtonHtml()
    {
        $themeId = $this->getTheme()->getId();
        /** @var $assignButton Mage_Backend_Block_Widget_Button */
        $assignButton = $this->getLayout()->createBlock('Mage_Backend_Block_Widget_Button');
        $assignButton->setData(array(
            'label'   => $this->__('Choose This Theme'),
            'data_attr'  => array(
                'widget-button' => array(
                    'event' => 'assign',
                    'related' => 'body',
                    'eventData' => array(
                        'theme_id' => $themeId
                    )
                ),
            ),
            'class'   => 'save action-theme-assign',
            'target'  => '_blank'
        ));

        return $assignButton->toHtml();
    }
}
