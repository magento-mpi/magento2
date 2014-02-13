<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_FullPageCache
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\FullPageCache\Model\Container;

/**
 * Banner widget container, renders and caches banner content.
 *
 * @category    Magento
 * @package     Magento_FullPageCache
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Banner extends \Magento\FullPageCache\Model\Container\AbstractContainer
{

    /**
     * Array of ids of banner chosen to be shown to user this time
     *
     * @var int[]
     */
    protected $_bannersSelected = null;

    /**
     * Array of ids of banners already shown during current series
     *
     * @var int[]
     */
    protected $_bannersSequence = null;

    /**
     * Get cache additional identifiers from cookies.
     * Customers are differentiated because they can have different content of banners (due to template variables)
     * or different sets of banners targeted to their segment.
     *
     * @return string
     */
    protected function _getIdentifier()
    {
        return $this->_getCookieValue(\Magento\FullPageCache\Model\Cookie::COOKIE_CUSTOMER, '');
    }

    /**
     * Returns cache identifier for informational data about customer banners
     *
     * @return string
     */
    protected function _getInfoCacheId()
    {
        return 'BANNER_INFORMATION_'
            . md5($this->_placeholder->getAttribute('cache_id')
            . '_' . $this->_getIdentifier());
    }

    /**
     * Saves informational cache, containing parameters used to show banners.
     * We don't use _saveCache() method internally, because it replaces sid in cache, that can be done only
     * after app is started, while this method can be called without app after rendering serie/shuffle banners.
     *
     * @param array $renderedParams
     * @return $this
     */
    protected function _saveInfoCache($renderedParams)
    {
        $data = serialize($renderedParams);
        $id = $this->_getInfoCacheId();
        $tags = array(\Magento\FullPageCache\Model\Processor::CACHE_TAG);
        $lifetime = $this->_placeholder->getAttribute('cache_lifetime');
        if (!$lifetime) {
            $lifetime = false;
        }

        $this->_fpcCache->save($data, $id, $tags, $lifetime);
        return $this;
    }

    /**
     * Loads informational cache, containing parameters used to show banners
     *
     * @return false|array
     */
    protected function _loadInfoCache()
    {
        $infoCacheId = $this->_getInfoCacheId();
        $data = $this->_loadCache($infoCacheId);
        if ($data === false) {
            return false;
        }
        return unserialize($data);
    }

    /**
     * Get cache identifier for banner block contents.
     * Used only after rendered banners are selected.
     *
     * @return string
     */
    protected function _getCacheId()
    {
        if ($this->_bannersSelected === null) {
            return false;
        }

        sort($this->_bannersSelected);
        return 'CONTAINER_BANNER_'
            . md5($this->_placeholder->getAttribute('cache_id')
            . '_' . $this->_getIdentifier())
            . '_' . implode(',', $this->_bannersSelected)
            . '_' .  self::_getCookieValue(\Magento\FullPageCache\Model\Cookie::CUSTOMER_SEGMENT_IDS, '');
    }

    /**
     * Generates placeholder content before application was initialized and applies it to page content if possible.
     * First we get meta-data with list of prepared banner ids and shown ids. Then we select banners to render and
     * check whether we already have that content in cache.
     *
     * @param string &$content
     * @return bool
     */
    public function applyWithoutApp(&$content)
    {
        // Load information about rendering process for current user
        $renderedParams = $this->_loadInfoCache();
        if ($renderedParams === false) {
            return false;
        }

        if (isset($renderedParams['bannersSequence'])) {
            $this->_bannersSequence = $renderedParams['bannersSequence'];
        }

        // Find a banner block to be rendered for this user
        $this->_bannersSelected = $this->_selectBannersToRender($renderedParams);
        if ($this->_bannersSelected) {
            $cacheId = $this->_getCacheId();
            $block = $this->_loadCache($cacheId);
        } else {
            // No banners to render - just fill with empty content
            $block = '';
        }

        if ($block !== false) {
            $this->_applyToContent($content, $block);
            return true;
        }
        return false;
    }

    /**
     * Selects the banners we want to show to the current customer.
     * The banners depend on the list of banner ids and rotation mode, that chooses banners to show from that list.
     *
     * @param array $renderedParams
     * @return int[]
     */
    protected function _selectBannersToRender($renderedParams)
    {
        $bannerIds = $renderedParams['bannerIds'];
        if (!$bannerIds) {
            return array();
        }

        $rotate = $this->_placeholder->getAttribute('rotate');
        switch ($rotate) {
            case \Magento\Banner\Block\Widget\Banner::BANNER_WIDGET_RORATE_RANDOM:
                $bannerId = $bannerIds[array_rand($bannerIds, 1)];
                $result = array($bannerId);
                break;

            case \Magento\Banner\Block\Widget\Banner::BANNER_WIDGET_RORATE_SERIES:
            case \Magento\Banner\Block\Widget\Banner::BANNER_WIDGET_RORATE_SHUFFLE:
                $isShuffle = $rotate == \Magento\Banner\Block\Widget\Banner::BANNER_WIDGET_RORATE_SHUFFLE;
                $bannerId = null;

                $bannersSequence = isset($renderedParams['bannersSequence']) ?
                    $renderedParams['bannersSequence'] :
                    array();
                if ($bannersSequence) {
                    $canShowIds = array_merge(array_diff($bannerIds, $bannersSequence), array());
                    if (!empty($canShowIds)) {
                        // Stil not whole serie is shown, choose the banner to show
                        $showKey = $isShuffle ? array_rand($canShowIds, 1) : 0;
                        $bannerId = $canShowIds[$showKey];
                        $bannersSequence[] = $bannerId;
                    }
                }

                // Start new serie (either no banners has been shown at all or whole serie has been shown)
                if (!$bannerId) {
                    $bannerKey = $isShuffle ? array_rand($bannerIds, 1) : 0;
                    $bannerId = $bannerIds[$bannerKey];
                    $bannersSequence = array($bannerId);
                }

                $renderedParams['bannersSequence'] = $bannersSequence;
                $this->_saveInfoCache($renderedParams); // So that serie progresses
                $result = array($bannerId);
                break;

            default:
                $result = $bannerIds;
        }

        return $result;
    }

    /**
     * Render banner block content
     *
     * @return string
     */
    protected function _renderBlock()
    {
        $block = $this->_getPlaceHolderBlock();
        $placeholder = $this->_placeholder;

        $parameters = array('name', 'types', 'display_mode', 'rotate', 'banner_ids', 'unique_id');
        foreach ($parameters as $parameter) {
            $value = $placeholder->getAttribute($parameter);
            $block->setData($parameter, $value);
        }

        /**
         * Ask block to render banners that we have selected. However block is not required to render that banners,
         * because something could change and these banners are not suitable any more (e.g. deleted, customer
         * changed his segment/group and so on) - in this case banner block will render suitable banner and return
         * new info options.
         */
        $suggestedParams = array();
        $suggestedParams['bannersSelected'] = $this->_bannersSelected;
        $suggestedParams['bannersSequence'] = $this->_bannersSequence;

        $this->_eventManager->dispatch('render_block', array('block' => $block, 'placeholder' => $this->_placeholder));

        $renderedInfo = $block->setSuggestedParams($suggestedParams)
            ->setTemplate($placeholder->getAttribute('template'))
            ->renderAndGetInfo();

        $renderedParams = $renderedInfo['params'];
        $this->_bannersSelected = $renderedParams['renderedBannerIds']; // Later _getCacheId() will use it
        unset($renderedParams['renderedBannerIds']); // We don't need it in cache info params
        $this->_saveInfoCache($renderedParams); // Save sequence params and possibly changed other params

        return $renderedInfo['html'];
    }
}
