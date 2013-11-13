<?php
/**
 * Plugin for layout service that removes non-VDE JavaScript assets in design mode
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\DesignEditor\Model\View\Action\LayoutService\Plugin;

use Magento\Core\Model\Page,
    Magento\View\LayoutInterface,
    Magento\App\ActionFlag;

class DesignEditor
{
    /**
     * @var Page
     */
    protected $_page;

    /**
     * @var LayoutInterface
     */
    protected $_layout;

    /**
     * @var ActionFlag
     */
    protected $_flag;

    /**
     * @param Page $page
     * @param LayoutInterface $layout
     * @param ActionFlag $flag
     */
    public function __construct(
        Page $page,
        LayoutInterface $layout,
        ActionFlag $flag
    ) {
        $this->_page = $page;
        $this->_layout = $layout;
        $this->_flag = $flag;
    }

    /**
     * Remove non-VDE JavaScript assets in design mode
     * Applicable in combination with enabled 'vde_design_mode' flag for 'head' block
     *
     * @param array $arguments
     * @param \Magento\Code\Plugin\InvocationChain $invocationChain
     * @return \Magento\View\Action\LayoutServiceInterface
     */
    public function aroundGenerateLayoutBlocks(array $arguments, \Magento\Code\Plugin\InvocationChain $invocationChain)
    {
        $layoutService = $invocationChain->proceed($arguments);

        if (!$this->_flag->get('', \Magento\App\Action\Action::FLAG_NO_DISPATCH_BLOCK_EVENT)) {
            $blockHead = $this->_layout->getBlock('head');
            if (!$blockHead || !$blockHead->getData('vde_design_mode')) {
                return $layoutService;
            }

            /** @var $pageAssets \Magento\Page\Model\Asset\GroupedCollection */
            $pageAssets = $this->_page->getAssets();

            $vdeAssets = array();
            foreach ($pageAssets->getGroups() as $group) {
                if ($group->getProperty('flag_name') == 'vde_design_mode') {
                    $vdeAssets = array_merge($vdeAssets, $group->getAll());
                }
            }

            /** @var $nonVdeAssets \Magento\Core\Model\Page\Asset\AssetInterface[] */
            $nonVdeAssets = array_diff_key($pageAssets->getAll(), $vdeAssets);

            foreach ($nonVdeAssets as $assetId => $asset) {
                if ($asset->getContentType() == \Magento\View\Publisher::CONTENT_TYPE_JS) {
                    $pageAssets->remove($assetId);
                }
            }
        }

        return $layoutService;
    }
}
