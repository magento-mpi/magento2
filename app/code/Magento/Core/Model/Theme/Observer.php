<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Core
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Theme Observer model
 */
class Magento_Core_Model_Theme_Observer
{
    /**
     * @var Magento_Core_Model_Theme_ImageFactory
     */
    protected $_themeImageFactory;

    /**
     * @var Magento_Core_Model_Resource_Layout_Update_Collection
     */
    protected $_updateCollection;

    /**
     * @var Magento_Theme_Model_Config_Customization
     */
    protected $_themeConfig;

    /**
     * @var Magento_Core_Model_Event_Manager
     */
    protected $_eventDispatcher;

    /**
     * @param Magento_Core_Model_Theme_ImageFactory $themeImageFactory
     * @param Magento_Core_Model_Resource_Layout_Update_Collection $updateCollection
     * @param Magento_Theme_Model_Config_Customization $themeConfig
     * @param Magento_Core_Model_Event_Manager $eventDispatcher
     */
    public function __construct(
        Magento_Core_Model_Theme_ImageFactory $themeImageFactory,
        Magento_Core_Model_Resource_Layout_Update_Collection $updateCollection,
        Magento_Theme_Model_Config_Customization $themeConfig,
        Magento_Core_Model_Event_Manager $eventDispatcher
    ) {
        $this->_themeImageFactory = $themeImageFactory;
        $this->_updateCollection = $updateCollection;
        $this->_themeConfig = $themeConfig;
        $this->_eventDispatcher = $eventDispatcher;
    }

    /**
     * Clean related contents to a theme (before save)
     *
     * @param Magento_Event_Observer $observer
     * @throws Magento_Core_Exception
     */
    public function cleanThemeRelatedContent(Magento_Event_Observer $observer)
    {
        $theme = $observer->getEvent()->getData('theme');
        if ($theme instanceof Magento_Core_Model_Theme) {
            return;
        }
        /** @var $theme Magento_Core_Model_Theme */
        if ($this->_themeConfig->isThemeAssignedToStore($theme)) {
            throw new Magento_Core_Exception(__('Theme isn\'t deletable.'));
        }
        $this->_themeImageFactory->create(array('theme' => $theme))->removePreviewImage();
        $this->_updateCollection->addThemeFilter($theme->getId())->delete();
    }

    /**
     * Check a theme, it's assigned to any of store
     *
     * @param Magento_Event_Observer $observer
     */
    public function checkThemeIsAssigned(Magento_Event_Observer $observer)
    {
        $theme = $observer->getEvent()->getData('theme');
        if ($theme instanceof Magento_Core_Model_Theme) {
            /** @var $theme Magento_Core_Model_Theme */
            if ($this->_themeConfig->isThemeAssignedToStore($theme)) {
                $this->_eventDispatcher->dispatch('assigned_theme_changed', array('theme' => $this));
            }
        }
    }
}
