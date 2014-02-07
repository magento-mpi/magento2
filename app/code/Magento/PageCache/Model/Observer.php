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
     * Path to varnish setting about if the Varnish is enabled
     */
    const XML_PATH_VARNISH_ENABLED = 'system/varnish_configuration_settings/caching_application';

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
        /** @var \Magento\Core\Model\Layout $layout */
        $layout = $observer->getEvent()->getLayout();
        $varnishIsEnabledFlag = $this->_config->isSetFlag(self::XML_PATH_VARNISH_ENABLED);
        if ($layout->isCacheable()) {
            $name = $observer->getEvent()->getElementName();
            $block = $layout->getBlock($name);
            if ($block instanceof \Magento\View\Element\AbstractBlock) {
                if ($varnishIsEnabledFlag && $block->getTtl()) {
                    $transport = $observer->getEvent()->getTransport();
                    $url = $block->getUrl(
                        'page_cache/block/wrapesi',
                        [
                            'blockname' => $block->getNameInLayout(),
                            'handles' => serialize($layout->getUpdate()->getHandles())
                        ]
                    );
                    $html = sprintf('<esi:include src="%s" />', $url);
                    $transport->setData('output', $html);
                } elseif ($block->isScopePrivate()) {
                    $transport = $observer->getEvent()->getTransport();
                    $output = $transport->getData('output');
                    $html = sprintf('<!-- BLOCK %1$s -->%2$s<!-- /BLOCK %1$s -->', $block->getNameInLayout(), $output);
                    $transport->setData('output', $html);
                }
            }
        }
    }
}
