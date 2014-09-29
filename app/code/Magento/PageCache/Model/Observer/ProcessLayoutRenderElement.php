<?php
/**
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\PageCache\Model\Observer;

class ProcessLayoutRenderElement
{
    /**
     * @var \Magento\PageCache\Helper\Data
     */
    protected $_helper;

    /**
     * Application config object
     *
     * @var \Magento\PageCache\Model\Config
     */
    protected $_config;

    /**
     * @param \Magento\PageCache\Model\Config $config
     * @param \Magento\PageCache\Helper\Data $helper
     */
    public function __construct(
        \Magento\PageCache\Model\Config $config,
        \Magento\PageCache\Helper\Data $helper
    ) {
        $this->_config = $config;
        $this->_helper = $helper;
    }

    /**
     * Replace the output of the block, containing ttl attribute, with ESI tag
     *
     * @param \Magento\Framework\View\Element\AbstractBlock $block
     * @return string
     */
    protected function _wrapEsi(\Magento\Framework\View\Element\AbstractBlock $block)
    {
        $url = $block->getUrl(
            'page_cache/block/esi',
            array(
                'blocks' => json_encode(array($block->getNameInLayout())),
                'handles' => json_encode($this->_helper->getActualHandles())
            )
        );
        return sprintf('<esi:include src="%s" />', $url);
    }

    /**
     * Add comment cache containers to private blocks
     * Blocks are wrapped only if page is cacheable
     *
     * @param \Magento\Framework\Event\Observer $observer
     * @return void
     */
    public function execute(\Magento\Framework\Event\Observer $observer)
    {
        $event = $observer->getEvent();
        /** @var \Magento\Framework\View\Layout $layout */
        $layout = $event->getLayout();
        if ($layout->isCacheable() && $this->_config->isEnabled()) {
            $name = $event->getElementName();
            $block = $layout->getBlock($name);
            $transport = $event->getTransport();
            if ($block instanceof \Magento\Framework\View\Element\AbstractBlock) {
                $blockTtl = $block->getTtl();
                $varnishIsEnabledFlag = ($this->_config->getType() == \Magento\PageCache\Model\Config::VARNISH);
                $output = $transport->getData('output');
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
}
