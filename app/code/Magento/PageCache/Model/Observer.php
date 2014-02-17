<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_PageCache
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\PageCache\Model;

/**
 * Class Observer
 * @package Magento\PageCache\Model
 */
class Observer
{
    /**
     * Application config object
     *
     * @var \Magento\App\ConfigInterface
     */
    protected $config;

    /**
     * PageCache helper
     *
     * @var \Magento\PageCache\Helper\Data
     */
    protected $helper;

    /**
     * Constructor
     *
     * @param \Magento\App\ConfigInterface   $config
     * @param \Magento\PageCache\Helper\Data $helper
     */
    public function __construct(
        \Magento\App\ConfigInterface $config,
        \Magento\PageCache\Helper\Data $helper
    ) {
        $this->config = $config;
        $this->helper = $helper;
    }

    /**
     * Add comment cache containers to private blocks
     * Blocks are wrapped only if page is cacheable
     *
     * @param \Magento\Event\Observer $observer
     */
    public function processLayoutRenderElement(\Magento\Event\Observer $observer)
    {
        $event = $observer->getEvent();
        /** @var \Magento\Core\Model\Layout $layout */
        $layout = $event->getLayout();
        if ($layout->isCacheable()) {
            $name = $event->getElementName();
            $block = $layout->getBlock($name);
            $transport = $event->getTransport();
            if ($block instanceof \Magento\View\Element\AbstractBlock) {
                $output = $transport->getData('output');
                $blockTtl = $block->getTtl();
                $varnishIsEnabledFlag = $this->config->isSetFlag(\Magento\PageCache\Model\Config::XML_PAGECACHE_TYPE);
                if ($varnishIsEnabledFlag && isset($blockTtl)) {
                    $output = $this->_wrapEsi($block);
                } elseif ($block->isScopePrivate()) {
                    $output = sprintf(
                        '<!-- BLOCK %1$s -->%2$s<!-- /BLOCK %1$s -->',
                        $block->getNameInLayout(),
                        $output
                    );
                }
                $transport->setData('output', $output);
            }
        }
    }

    /**
     * Replace the output of the block, containing ttl attribute, with ESI tag
     *
     * @param \Magento\View\Element\AbstractBlock $block
     * @return string
     */
    protected function _wrapEsi(
        \Magento\View\Element\AbstractBlock $block
    ) {
        $url = $block->getUrl(
            'page_cache/block/esi',
            [
                'blocks' => json_encode([$block->getNameInLayout()]),
                'handles' => json_encode($this->helper->getActualHandles())
            ]
        );
        return sprintf('<esi:include src="%s" />', $url);
    }
}
