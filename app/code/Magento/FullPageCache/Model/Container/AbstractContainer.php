<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_FullPageCache
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Abstract placeholder container
 */
namespace Magento\FullPageCache\Model\Container;

abstract class AbstractContainer implements \Magento\FullPageCache\Model\ContainerInterface
{
    /**
     * @var null|\Magento\FullPageCache\Model\Processor
     */
    protected $_processor;

    /**
     * Placeholder instance
     *
     * @var \Magento\FullPageCache\Model\Container\Placeholder
     */
    protected $_placeholder;

    /**
     * @var \Magento\FullPageCache\Model\Cache
     */
    protected $_fpcCache;

    /**
     * Core event manager proxy
     *
     * @var \Magento\Core\Model\Event\Manager
     */
    protected $_eventManager = null;

    /**
     * Core registry
     *
     * @var \Magento\Core\Model\Registry
     */
    protected $_coreRegistry = null;

    /**
     * @var \Magento\FullPageCache\Helper\Url
     */
    protected $_urlHelper;

    /**
     * Core store config
     *
     * @var \Magento\Core\Model\Store\Config
     */
    protected $_coreStoreConfig;

    /**
     * @param \Magento\Core\Model\Event\Manager $eventManager
     * @param \Magento\FullPageCache\Model\Cache $fpcCache
     * @param \Magento\FullPageCache\Model\Container\Placeholder $placeholder
     * @param \Magento\Core\Model\Registry $coreRegistry
     * @param \Magento\FullPageCache\Helper\Url $urlHelper
     * @param \Magento\Core\Model\Store\Config $coreStoreConfig
     */
    public function __construct(
        \Magento\Core\Model\Event\Manager $eventManager,
        \Magento\FullPageCache\Model\Cache $fpcCache,
        \Magento\FullPageCache\Model\Container\Placeholder $placeholder,
        \Magento\Core\Model\Registry $coreRegistry,
        \Magento\FullPageCache\Helper\Url $urlHelper,
        \Magento\Core\Model\Store\Config $coreStoreConfig
    ) {
        $this->_coreStoreConfig = $coreStoreConfig;
        $this->_placeholder = $placeholder;
        $this->_fpcCache = $fpcCache;
        $this->_eventManager = $eventManager;
        $this->_coreRegistry = $coreRegistry;
        $this->_urlHelper = $urlHelper;
    }

    /**
     * Get container individual cache id
     *
     * @return string|bool
     */
    protected function _getCacheId()
    {
        return false;
    }

    /**
     * Generate placeholder content before application was initialized and apply to page content if possible
     *
     * @param string $content
     * @return bool
     */
    public function applyWithoutApp(&$content)
    {
        $cacheId = $this->_getCacheId();

        if ($cacheId === false) {
            $this->_applyToContent($content, '');
            return true;
        }

        $block = $this->_loadCache($cacheId);
        if ($block === false) {
            return false;
        }

        $block = $this->_urlHelper->replaceUenc($block);
        $this->_applyToContent($content, $block);
        return true;
    }

    /**
     * Generate and apply container content in controller after application is initialized
     *
     * @param string $content
     * @return bool
     */
    public function applyInApp(&$content)
    {
        $blockContent = $this->_renderBlock();
        if ($blockContent === false) {
            return false;
        }

        if ($this->_coreStoreConfig->getConfig(\Magento\FullPageCache\Model\Processor::XML_PATH_CACHE_DEBUG)) {
            $debugBlock = \Mage::app()->getLayout()->createBlock('Magento\FullPageCache\Block\Debug');
            $debugBlock->setDynamicBlockContent($blockContent);
            $this->_applyToContent($content, $debugBlock->toHtml());
        } else {
            $this->_applyToContent($content, $blockContent);
        }

        $subprocessor = $this->_processor->getSubprocessor();
        if ($subprocessor) {
            $contentWithOutNestedBlocks = $subprocessor->replaceContentToPlaceholderReplacer($blockContent);
            $this->saveCache($contentWithOutNestedBlocks);
        }

        return true;
    }

    /**
     * Save rendered block content to cache storage
     *
     * @param string $blockContent
     * @return \Magento\FullPageCache\Model\Container\AbstractContainer
     */
    public function saveCache($blockContent)
    {
        $cacheId = $this->_getCacheId();
        if ($cacheId !== false) {
            $this->_saveCache($blockContent, $cacheId);
        }
        return $this;
    }

    /**
     * Render block content from placeholder
     *
     * @return string|false
     */
    protected function _renderBlock()
    {
        return false;
    }

    /**
     * Replace container placeholder in content on container content
     *
     * @param string $content
     * @param string $containerContent
     */
    protected function _applyToContent(&$content, $containerContent)
    {
        $containerContent = $this->_placeholder->getStartTag() . $containerContent . $this->_placeholder->getEndTag();
        $content = str_replace($this->_placeholder->getReplacer(), $containerContent, $content);
    }

    /**
     * Load cached data by cache id
     *
     * @param string $id
     * @return string|bool
     */
    protected function _loadCache($id)
    {
        return $this->_fpcCache->load($id);
    }

    /**
     * Save data to cache storage
     *
     * @param string $data
     * @param string $id
     * @param array $tags
     * @param null|int $lifetime
     * @return \Magento\FullPageCache\Model\Container\AbstractContainer
     */
    protected function _saveCache($data, $id, $tags = array(), $lifetime = null)
    {
        $tags[] = \Magento\FullPageCache\Model\Processor::CACHE_TAG;
        if (is_null($lifetime)) {
            $lifetime = $this->_placeholder->getAttribute('cache_lifetime') ?
                $this->_placeholder->getAttribute('cache_lifetime') : false;
        }

        /**
         * Replace all occurrences of session_id with unique marker
         */
        \Magento\FullPageCache\Helper\Url::replaceSid($data);

        $this->_fpcCache->save($data, $id, $tags, $lifetime);
        return $this;
    }

    /**
     * Retrieve cookie value
     *
     * @param string $cookieName
     * @param mixed $defaultValue
     * @return string
     */
    protected static function _getCookieValue($cookieName, $defaultValue = null)
    {
        return (array_key_exists($cookieName, $_COOKIE) ? $_COOKIE[$cookieName] : $defaultValue);
    }

    /**
     * Set processor for container needs
     *
     * @param \Magento\FullPageCache\Model\Processor $processor
     * @return \Magento\FullPageCache\Model\Container\AbstractContainer
     */
    public function setProcessor(\Magento\FullPageCache\Model\Processor $processor)
    {
        $this->_processor = $processor;
        return $this;
    }

    /**
     * Get last visited category id
     *
     * @return string|null
     */
    protected function _getCategoryId()
    {
        if ($this->_processor) {
            $categoryId = $this->_processor
                ->getMetadata(\Magento\FullPageCache\Model\Processor\Category::METADATA_CATEGORY_ID);
            if ($categoryId) {
                return $categoryId;
            }
        }

        //If it is not product page and not category page - we have no any category (not using last visited)
        if (!$this->_getProductId()) {
            return null;
        }

        return self::_getCookieValue(\Magento\FullPageCache\Model\Cookie::COOKIE_CATEGORY_ID, null);
    }

    /**
     * Get current product id
     *
     * @return string|null
     */
    protected function _getProductId()
    {
        if (!$this->_processor) {
            return null;
        }

        return $this->_processor
            ->getMetadata(\Magento\FullPageCache\Model\Processor\Product::METADATA_PRODUCT_ID);
    }

    /**
     * Get current request id
     *
     * @return string|null
     */
    protected function _getRequestId()
    {
        if (!$this->_processor) {
            return null;
        }

        return $this->_processor->getRequestId();
    }

    /**
     * Get Place Holder Block
     *
     * @return \Magento\Core\Block\AbstractBlock
     */
    protected function _getPlaceHolderBlock()
    {
        $blockName = $this->_placeholder->getAttribute('block');
        $block = \Mage::app()->getLayout()->createBlock($blockName);
        $block->setTemplate($this->_placeholder->getAttribute('template'));
        $block->setLayout(\Mage::app()->getLayout());
        $block->setSkipRenderTag(true);
        return $block;
    }
}
