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
     * @var \Magento\App\PageCache\Cache
     */
    protected $_cache;

    /**
     * @var \Magento\PageCache\Helper\Data
     */
    protected $_helper;

    /**
     * Constructor
     *
     * @param \Magento\App\ConfigInterface $config
     * @param \Magento\App\PageCache\Cache $cache
     * @param \Magento\PageCache\Helper\Data $helper
     */
    public function __construct(
        \Magento\App\ConfigInterface $config,
        \Magento\App\PageCache\Cache $cache,
        \Magento\PageCache\Helper\Data $helper
    ){
        $this->_config = $config;
        $this->_cache = $cache;
        $this->_helper = $helper;
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
                $varnishIsEnabledFlag = $this->_config->isSetFlag(\Magento\PageCache\Model\Config::XML_PAGECACHE_TYPE);
                if ($varnishIsEnabledFlag && isset($blockTtl)) {
                    $output = $this->_wrapEsi($block, $layout);
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
                'handles' => json_encode($layout->getUpdate()->getHandles())
            ]
        );
        return sprintf('<esi:include src="%s" />', $url);
    }

    /**
     * If Built-In caching is enabled it collects array of tags
     * of incoming object and asks to clean cache.
     *
     * @param \Magento\Event\Observer $observer
     */
    public function invalidateCache(\Magento\Event\Observer $observer)
    {
        $tags = $observer->getIdentities();
        if($observer instanceof \Magento\Object\IdentityInteface) {
            if($this->_config->getType() == \Magento\PageCache\Model\Config::BUILT_IN)
            {
                $this->_cache->clean($tags);
            } else {
                $preparedTags = implode('|', $tags);
                $curl = curl_init($this->_helper->getUrl('*'));
                curl_setopt($curl, CURLOPT_CUSTOMREQUEST, "PURGE");
                curl_setopt($curl, CURLOPT_HTTPHEADER, "X-Magento-Tags-Pattern: {$preparedTags}");
                curl_exec($curl);
            }
        }
    }

}
