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
namespace Magento\Core\Model\Theme;

class Observer
{
    /**
     * @var Magento_Core_Model_Theme_ImageFactory
     */
    protected $_themeImageFactory;

    /**
     * @var \Magento\Core\Model\Resource\Layout\Update\Collection
     */
    protected $_updateCollection;

    /**
     * @var \Magento\Theme\Model\Config\Customization
     */
    protected $_themeConfig;

    /**
     * @var \Magento\Core\Model\Event\Manager
     */
    protected $_eventDispatcher;

    /**
     * @param Magento_Core_Model_Theme_ImageFactory $themeImageFactory
     * @param \Magento\Core\Model\Resource\Layout\Update\Collection $updateCollection
     * @param \Magento\Theme\Model\Config\Customization $themeConfig
     * @param \Magento\Core\Model\Event\Manager $eventDispatcher
     */
    public function __construct(
        Magento_Core_Model_Theme_ImageFactory $themeImageFactory,
        \Magento\Core\Model\Resource\Layout\Update\Collection $updateCollection,
        \Magento\Theme\Model\Config\Customization $themeConfig,
        \Magento\Core\Model\Event\Manager $eventDispatcher
    ) {
        $this->_themeImageFactory = $themeImageFactory;
        $this->_updateCollection = $updateCollection;
        $this->_themeConfig = $themeConfig;
        $this->_eventDispatcher = $eventDispatcher;
    }

    /**
     * Clean related contents to a theme (before save)
     *
     * @param \Magento\Event\Observer $observer
     * @throws \Magento\Core\Exception
     */
    public function cleanThemeRelatedContent(\Magento\Event\Observer $observer)
    {
        $theme = $observer->getEvent()->getData('theme');
        if ($theme instanceof \Magento\Core\Model\Theme) {
            return;
        }
        /** @var $theme \Magento\Core\Model\Theme */
        if ($this->_themeConfig->isThemeAssignedToStore($theme)) {
            throw new \Magento\Core\Exception(__('Theme isn\'t deletable.'));
        }
        $this->_themeImageFactory->create(array('theme' => $theme))->removePreviewImage();
        $this->_updateCollection->addThemeFilter($theme->getId())->delete();
    }

    /**
     * Check a theme, it's assigned to any of store
     *
     * @param \Magento\Event\Observer $observer
     */
    public function checkThemeIsAssigned(\Magento\Event\Observer $observer)
    {
        $theme = $observer->getEvent()->getData('theme');
        if ($theme instanceof \Magento\Core\Model\Theme) {
            /** @var $theme \Magento\Core\Model\Theme */
            if ($this->_themeConfig->isThemeAssignedToStore($theme)) {
                $this->_eventDispatcher->dispatch('assigned_theme_changed', array('theme' => $this));
            }
        }
    }
}
