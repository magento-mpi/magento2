<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\DesignEditor\Model;

use Magento\Framework\Event\Observer as EventObserver;

/**
 * Observer for design editor module
 */
class Observer
{
    /**
     * @var \Magento\Framework\ObjectManager
     */
    protected $_objectManager;

    /**
     * @var \Magento\DesignEditor\Helper\Data
     */
    protected $_helper;

    /**
     * @param \Magento\Framework\ObjectManager $objectManager
     * @param \Magento\DesignEditor\Helper\Data $helper
     */
    public function __construct(\Magento\Framework\ObjectManager $objectManager, \Magento\DesignEditor\Helper\Data $helper)
    {
        $this->_objectManager = $objectManager;
        $this->_helper = $helper;
    }

    /**
     * Remove non-VDE JavaScript assets in design mode
     * Applicable in combination with enabled 'vde_design_mode' flag for 'head' block
     *
     * @param EventObserver $event
     * @return void
     */
    public function clearJs(EventObserver $event)
    {
        /** @var $layout \Magento\Framework\View\LayoutInterface */
        $layout = $event->getEvent()->getLayout();
        $blockHead = $layout->getBlock('head');
        if (!$blockHead || !$blockHead->getData('vde_design_mode')) {
            return;
        }

        /** @var $pageAssets \Magento\Framework\View\Asset\GroupedCollection */
        $pageAssets = $this->_objectManager->get('Magento\Framework\View\Asset\GroupedCollection');

        $vdeAssets = array();
        foreach ($pageAssets->getGroups() as $group) {
            if ($group->getProperty('flag_name') == 'vde_design_mode') {
                $vdeAssets = array_merge($vdeAssets, $group->getAll());
            }
        }

        /** @var $nonVdeAssets \Magento\Framework\View\Asset\AssetInterface[] */
        $nonVdeAssets = array_diff_key($pageAssets->getAll(), $vdeAssets);

        foreach ($nonVdeAssets as $assetId => $asset) {
            if ($asset->getContentType() == \Magento\Framework\View\Publisher::CONTENT_TYPE_JS) {
                $pageAssets->remove($assetId);
            }
        }
    }

    /**
     * Save quick styles
     *
     * @param EventObserver $event
     * @return void
     */
    public function saveQuickStyles($event)
    {
        /** @var $configuration \Magento\DesignEditor\Model\Editor\Tools\Controls\Configuration */
        $configuration = $event->getData('configuration');
        /** @var $theme \Magento\Framework\View\Design\ThemeInterface */
        $theme = $event->getData('theme');
        if ($configuration->getControlConfig() instanceof \Magento\DesignEditor\Model\Config\Control\QuickStyles) {
            /** @var $renderer \Magento\DesignEditor\Model\Editor\Tools\QuickStyles\Renderer */
            $renderer = $this->_objectManager->create('Magento\DesignEditor\Model\Editor\Tools\QuickStyles\Renderer');
            $content = $renderer->render($configuration->getAllControlsData());
            /** @var $cssService \Magento\DesignEditor\Model\Theme\Customization\File\QuickStyleCss */
            $cssService = $this->_objectManager->create(
                'Magento\DesignEditor\Model\Theme\Customization\File\QuickStyleCss'
            );
            /** @var $singleFile \Magento\Theme\Model\Theme\SingleFile */
            $singleFile = $this->_objectManager->create(
                'Magento\Theme\Model\Theme\SingleFile',
                array('fileService' => $cssService)
            );
            $singleFile->update($theme, $content);
        }
    }

    /**
     * Save time stamp of last change
     *
     * @param EventObserver $event
     * @return void
     */
    public function saveChangeTime($event)
    {
        /** @var $theme \Magento\Core\Model\Theme|null */
        $theme = $event->getTheme() ?: $event->getDataObject()->getTheme();
        /** @var $change \Magento\DesignEditor\Model\Theme\Change */
        $change = $this->_objectManager->create('Magento\DesignEditor\Model\Theme\Change');
        if ($theme && $theme->getId()) {
            $change->loadByThemeId($theme->getId());
            $change->setThemeId($theme->getId())->setChangeTime(null);
            $change->save();
        }
    }
}
