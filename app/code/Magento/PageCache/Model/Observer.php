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
    protected $_config;

    /**
     * Constructor
     *
     * @param \Magento\App\ConfigInterface $config
     */
    public function __construct(\Magento\App\ConfigInterface $config)
    {
        $this->_config = $config;
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
        $varnishIsEnabledFlag = $this->_config->isSetFlag(\Magento\PageCache\Model\Config::XML_PATH_VARNISH_ENABLED);
        if (!$layout->isCacheable()) {
            $name = $event->getElementName();
            $block = $layout->getBlock($name);
            $transport = $event->getTransport();
            if ($block instanceof \Magento\View\Element\AbstractBlock) {
                $output = $transport->getData('output');
                $blockTtl = $block->getTtl();
                if ($varnishIsEnabledFlag && isset($blockTtl)) {
                    $output = $this->_wrapEsi($block, $layout);
                } elseif ($block->isScopePrivate()) {
                    $output = sprintf('<!-- BLOCK %1$s -->%2$s<!-- /BLOCK %1$s -->', $block->getNameInLayout(), $output);
                }
                $transport->setData('output', $output);
            }
        }
    }

    /**
     * Replace the output of the block with ttl with ESI tag
     *
     * @param \Magento\View\Element\AbstractBlock $block
     * @param \Magento\Core\Model\Layout $layout
     * @return string
     */
    protected function _wrapEsi(
        \Magento\View\Element\AbstractBlock $block,
        \Magento\Core\Model\Layout $layout
    ) {

        $url = $block->getUrl(
            'page_cache/block/esi',
            [
                'blocks' => json_encode([$block->getNameInLayout()]),
                'handles'   => json_encode($layout->getUpdate()->getHandles())
            ]
        );
        return sprintf('<esi:include src="%s" />', $url);
    }
}
