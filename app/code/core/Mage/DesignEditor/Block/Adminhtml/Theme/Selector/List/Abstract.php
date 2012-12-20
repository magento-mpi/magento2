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
 * Abstract theme list
 *
 * @method Mage_Core_Model_Resource_Theme_Collection getCollection()
 * @method Mage_Backend_Block_Abstract setCollection(Mage_Core_Model_Resource_Theme_Collection $collection)
 *
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 */
abstract class Mage_DesignEditor_Block_Adminhtml_Theme_Selector_List_Abstract
    extends Mage_Backend_Block_Abstract
{
    /**
     * Application model
     *
     * @var Mage_Core_Model_App
     */
    protected $_app;

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
     * @param Mage_Core_Model_App $app
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
        Mage_Core_Model_App $app,
        array $data = array()
    ) {
        $this->_app = $app;
        parent::__construct($request, $layout, $eventManager, $urlBuilder, $translator, $cache, $designPackage,
            $session, $storeConfig, $frontController, $helperFactory, $data
        );
    }

    /**
     * Get tab title
     *
     * @return string
     */
    abstract public function getTabTitle();

    /**
     * Add theme buttons
     *
     * @param Mage_DesignEditor_Block_Adminhtml_Theme $themeBlock
     * @return Mage_DesignEditor_Block_Adminhtml_Theme_Selector_List_Abstract
     */
    protected function _addThemeButtons($themeBlock)
    {
        $themeBlock->clearButtons();
        return $this;
    }

    /**
     * Get list items of themes
     *
     * @return array
     */
    public function getListItems()
    {
        /** @var $itemBlock Mage_DesignEditor_Block_Adminhtml_Theme */
        $itemBlock = $this->getChildBlock('theme');

        $themeCollection = $this->getCollection();

        $items = array();
        /** @var $theme Mage_Core_Model_Theme */
        foreach ($themeCollection as $theme) {
            $itemBlock->setTheme($theme);
            $this->_addThemeButtons($itemBlock);
            $items[] = $this->getChildHtml('theme', false);
        }

        return $items;
    }

    /**
     * Get assign to storeview button
     *
     * @param Mage_DesignEditor_Block_Adminhtml_Theme $themeBlock
     * @return string
     */
    protected function _addAssignButtonHtml($themeBlock)
    {
        $themeId = $themeBlock->getTheme()->getId();
        /** @var $assignButton Mage_Backend_Block_Widget_Button */
        $assignButton = $this->getLayout()->createBlock('Mage_Backend_Block_Widget_Button');
        $assignButton->setData(array(
            'label'   => $this->__('Assign to a Storeview'),
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

        $themeBlock->addButton($assignButton);
        return $this;
    }

    /**
     * Get preview button
     *
     * @param Mage_DesignEditor_Block_Adminhtml_Theme $themeBlock
     * @return string
     */
    protected function _addPreviewButtonHtml($themeBlock)
    {
        /** @var $previewButton Mage_Backend_Block_Widget_Button */
        $previewButton = $this->getLayout()->createBlock('Mage_Backend_Block_Widget_Button');
        $previewButton->setData(array(
            'label'     => $this->__('Preview Theme'),
            'class'     => 'preview-default',
            'data_attr' => array(
                'widget-button' => array(
                    'event' => 'preview',
                    'related' => 'body',
                    'eventData' => array(
                        'preview_url' => $this->_getPreviewUrl(
                            Mage_DesignEditor_Model_Theme_PreviewFactory::TYPE_DEFAULT, $themeBlock->getTheme()->getId()
                        )
                    )
                ),
            )
        ));

        $themeBlock->addButton($previewButton);
        return $this;
    }

    /**
     * Get edit button
     *
     * @param Mage_DesignEditor_Block_Adminhtml_Theme $themeBlock
     * @return string
     */
    protected function _addEditButtonHtml($themeBlock)
    {
        /** @var $editButton Mage_Backend_Block_Widget_Button */
        $editButton = $this->getLayout()->createBlock('Mage_Backend_Block_Widget_Button');

        $editButton->setData(array(
            'label'     => $this->__('Edit Button'),
            'class'     => 'add edit-theme',
            'data_attr' => array(
                'widget-button' => array(
                    'event' => 'edit',
                    'related' => 'body',
                    'eventData' => array(
                        'edit_url' => $this->_getEditUrl($themeBlock->getTheme())
                    )
                ),
            ),
        ));

        $themeBlock->addButton($editButton);
        return $this;
    }

    /**
     * Get preview url for selected theme
     *
     * @param string $previewType
     * @param int $themeId
     * @return string
     */
    protected function _getPreviewUrl($previewType, $themeId)
    {
        return $this->getUrl('*/*/preview', array(
            Mage_DesignEditor_Block_Adminhtml_Theme_Preview::PARAM_THEME_ID => $themeId,
            Mage_DesignEditor_Block_Adminhtml_Theme_Preview::PARAM_PREVIEW  => $previewType
        ));
    }

    /**
     * Get edit theme url
     *
     * @param Mage_Core_Model_Theme $theme
     * @return string
     */
    protected function _getEditUrl($theme)
    {
        /** @var $store Mage_Core_Model_Store */
        $stores = $theme->getAssignedStores();
        $store = $stores ? reset($stores) : $this->_app->getDefaultStoreView();

        return $this->getUrl('*/*/run', array(
            'theme_id' => $theme->getId(),
            'store_id' => $store->getId(),
        ));
    }
}
