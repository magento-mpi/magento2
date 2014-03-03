<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Core
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Core\Model\Theme;

use Magento\Core\Exception;
use Magento\Event\Observer as EventObserver;

/**
 * Theme Observer model
 */
class Observer
{
    /**
     * @var \Magento\View\Design\Theme\ImageFactory
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
     * @var \Magento\Event\ManagerInterface
     */
    protected $_eventDispatcher;

    /**
     * @param \Magento\View\Design\Theme\ImageFactory $themeImageFactory
     * @param \Magento\Core\Model\Resource\Layout\Update\Collection $updateCollection
     * @param \Magento\Theme\Model\Config\Customization $themeConfig
     * @param \Magento\Event\ManagerInterface $eventDispatcher
     */
    public function __construct(
        \Magento\View\Design\Theme\ImageFactory $themeImageFactory,
        \Magento\Core\Model\Resource\Layout\Update\Collection $updateCollection,
        \Magento\Theme\Model\Config\Customization $themeConfig,
        \Magento\Event\ManagerInterface $eventDispatcher
    ) {
        $this->_themeImageFactory = $themeImageFactory;
        $this->_updateCollection = $updateCollection;
        $this->_themeConfig = $themeConfig;
        $this->_eventDispatcher = $eventDispatcher;
    }

    /**
     * Clean related contents to a theme (before save)
     *
     * @param EventObserver $observer
     * @return void
     * @throws Exception
     */
    public function cleanThemeRelatedContent(EventObserver $observer)
    {
        $theme = $observer->getEvent()->getData('theme');
        if ($theme instanceof \Magento\View\Design\ThemeInterface) {
            return;
        }
        /** @var $theme \Magento\View\Design\ThemeInterface */
        if ($this->_themeConfig->isThemeAssignedToStore($theme)) {
            throw new Exception(__('Theme isn\'t deletable.'));
        }
        $this->_themeImageFactory->create(array('theme' => $theme))->removePreviewImage();
        $this->_updateCollection->addThemeFilter($theme->getId())->delete();
    }

    /**
     * Check a theme, it's assigned to any of store
     *
     * @param EventObserver $observer
     * @return void
     */
    public function checkThemeIsAssigned(EventObserver $observer)
    {
        $theme = $observer->getEvent()->getData('theme');
        if ($theme instanceof \Magento\View\Design\ThemeInterface) {
            /** @var $theme \Magento\View\Design\ThemeInterface */
            if ($this->_themeConfig->isThemeAssignedToStore($theme)) {
                $this->_eventDispatcher->dispatch('assigned_theme_changed', array('theme' => $this));
            }
        }
    }
}
