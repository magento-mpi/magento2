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
 * Observer for design editor module
 */
class Magento_DesignEditor_Model_Observer
{
    /**
     * @var Magento_ObjectManager
     */
    protected $_objectManager;

    /**
     * @var Magento_DesignEditor_Helper_Data
     */
    protected $_helper;

    /**
     * @param Magento_ObjectManager $objectManager
     * @param Magento_DesignEditor_Helper_Data $helper
     */
    public function __construct(
        Magento_ObjectManager $objectManager,
        Magento_DesignEditor_Helper_Data $helper
    ) {
        $this->_objectManager = $objectManager;
        $this->_helper        = $helper;
    }

    /**
     * Remove non-VDE JavaScript assets in design mode
     * Applicable in combination with enabled 'vde_design_mode' flag for 'head' block
     *
     * @param Magento_Event_Observer $event
     */
    public function clearJs(Magento_Event_Observer $event)
    {
        /** @var $layout Magento_Core_Model_Layout */
        $layout = $event->getEvent()->getLayout();
        $blockHead = $layout->getBlock('head');
        if (!$blockHead || !$blockHead->getData('vde_design_mode')) {
            return;
        }

        /** @var $page Magento_Core_Model_Page */
        $page = $this->_objectManager->get('Magento_Core_Model_Page');

        /** @var $pageAssets Magento_Page_Model_Asset_GroupedCollection */
        $pageAssets = $page->getAssets();

        $vdeAssets = array();
        foreach ($pageAssets->getGroups() as $group) {
            if ($group->getProperty('flag_name') == 'vde_design_mode') {
                $vdeAssets = array_merge($vdeAssets, $group->getAll());
            }
        }

        /** @var $nonVdeAssets Magento_Core_Model_Page_Asset_AssetInterface[] */
        $nonVdeAssets = array_diff_key($pageAssets->getAll(), $vdeAssets);

        foreach ($nonVdeAssets as $assetId => $asset) {
            if ($asset->getContentType() == Magento_Core_Model_View_Publisher::CONTENT_TYPE_JS) {
                $pageAssets->remove($assetId);
            }
        }
    }

    /**
     * Save quick styles
     *
     * @param Magento_Event_Observer $event
     */
    public function saveQuickStyles($event)
    {
        /** @var $configuration Magento_DesignEditor_Model_Editor_Tools_Controls_Configuration */
        $configuration = $event->getData('configuration');
        /** @var $theme Magento_Core_Model_Theme */
        $theme = $event->getData('theme');
        if ($configuration->getControlConfig() instanceof Magento_DesignEditor_Model_Config_Control_QuickStyles) {
            /** @var $renderer Magento_DesignEditor_Model_Editor_Tools_QuickStyles_Renderer */
            $renderer = $this->_objectManager->create('Magento_DesignEditor_Model_Editor_Tools_QuickStyles_Renderer');
            $content = $renderer->render($configuration->getAllControlsData());
            /** @var $cssService Magento_DesignEditor_Model_Theme_Customization_File_QuickStyleCss */
            $cssService = $this->_objectManager->create(
                'Magento_DesignEditor_Model_Theme_Customization_File_QuickStyleCss'
            );
            /** @var $singleFile Magento_Theme_Model_Theme_SingleFile */
            $singleFile = $this->_objectManager->create('Magento_Theme_Model_Theme_SingleFile',
                array('fileService' => $cssService));
            $singleFile->update($theme, $content);
        }
    }

    /**
     * Save time stamp of last change
     *
     * @param Magento_Event_Observer $event
     */
    public function saveChangeTime($event)
    {
        /** @var $theme Magento_Core_Model_Theme|null */
        $theme = $event->getTheme() ?: $event->getDataObject()->getTheme();
        /** @var $change Magento_DesignEditor_Model_Theme_Change */
        $change = $this->_objectManager->create('Magento_DesignEditor_Model_Theme_Change');
        if ($theme && $theme->getId()) {
            $change->loadByThemeId($theme->getId());
            $change->setThemeId($theme->getId())->setChangeTime(null);
            $change->save();
        }
    }

    /**
     * Copy additional information about theme change time
     *
     * @param Magento_Event_Observer $event
     */
    public function copyChangeTime($event)
    {
        /** @var $sourceTheme Magento_Core_Model_Theme|null */
        $sourceTheme = $event->getData('sourceTheme');
        /** @var $targetTheme Magento_Core_Model_Theme|null */
        $targetTheme = $event->getData('targetTheme');
        if ($sourceTheme && $targetTheme) {
            /** @var $sourceChange Magento_DesignEditor_Model_Theme_Change */
            $sourceChange = $this->_objectManager->create('Magento_DesignEditor_Model_Theme_Change');
            $sourceChange->loadByThemeId($sourceTheme->getId());
            /** @var $targetChange Magento_DesignEditor_Model_Theme_Change */
            $targetChange = $this->_objectManager->create('Magento_DesignEditor_Model_Theme_Change');
            $targetChange->loadByThemeId($targetTheme->getId());

            if ($sourceChange->getId()) {
                $targetChange->setThemeId($targetTheme->getId());
                $targetChange->setChangeTime($sourceChange->getChangeTime());
                $targetChange->save();
            } elseif ($targetChange->getId()) {
                $targetChange->delete();
            }
        }
    }

    /**
     * Determine if the vde specific translation class should be used.
     *
     * @param  Magento_Event_Observer $observer
     * @return Magento_DesignEditor_Model_Observer
     */
    public function initializeTranslation(Magento_Event_Observer $observer)
    {
        if ($this->_helper->isVdeRequest()) {
            // Request is for vde.  Override the translation class.
            $observer->getResult()->setInlineType('Magento_DesignEditor_Model_Translate_InlineVde');
        }
        return $this;
    }
}
