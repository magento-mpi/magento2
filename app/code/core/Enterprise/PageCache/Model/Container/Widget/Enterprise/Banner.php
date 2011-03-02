<?php
/**
 * Magento Enterprise Edition
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Magento Enterprise Edition License
 * that is bundled with this package in the file LICENSE_EE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://www.magentocommerce.com/license/enterprise-edition
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@magentocommerce.com so we can send you a copy immediately.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade Magento to newer
 * versions in the future. If you wish to customize Magento for your
 * needs please refer to http://www.magentocommerce.com for more information.
 *
 * @category    Enterprise
 * @package     Enterprise_PageCache
 * @copyright   Copyright (c) 2010 Magento Inc. (http://www.magentocommerce.com)
 * @license     http://www.magentocommerce.com/license/enterprise-edition
 */

/**
 * Banner widget container, renders and caches banner content.
 *
 * @category    Enterprise
 * @package     Enterprise_PageCache
 * @copyright   Copyright (c) 2010 Magento Inc. (http://www.magentocommerce.com)
 * @license     http://www.magentocommerce.com/license/enterprise-edition
 */
class Enterprise_PageCache_Model_Container_Widget_Enterprise_Banner
    extends Enterprise_PageCache_Model_Container_Abstract
{
    /**
     * Checks whether banner contents can be cached.
     * This func can be called via _getCacheId() that is called before full app initialization. So we can check only
     * placeholder attributes, and cannot use models' paths and etc, because modules haven't been initialized yet.
     *
     * Only two cases of fixed banner ids are possible to cache without embedding banner widget logic and
     * making DB requests:
     * 1) Random/Series/Shuffle rotation mode with 1 banner to choose from
     * 2) None rotation mode (show all banners at once)
     *
     * @return bool
     */
    protected function _bannerCanBeCached()
    {
        $displayMode = $this->_placeholder->getAttribute('display_mode');
        if ($displayMode != Enterprise_Banner_Block_Widget_Banner::BANNER_WIDGET_DISPLAY_FIXED) {
            return false;
        }

        $rotate = $this->_placeholder->getAttribute('rotate');
        if ($rotate == Enterprise_Banner_Block_Widget_Banner::BANNER_WIDGET_RORATE_NONE) {
            return true;
        }
        $bannerIds = explode(',', $this->_placeholder->getAttribute('banner_ids'));
        return count($bannerIds) < 2;
    }

    /**
     * Get identifier from cookies.
     * Customers are differentiated because they can have different content of banners (due to template variables)
     * or different sets of banners targeted to their segment.
     *
     * @return string
     */
    protected function _getIdentifier()
    {
        return $this->_getCookieValue(Enterprise_PageCache_Model_Cookie::COOKIE_CUSTOMER, '');
    }

    /**
     * Get cache identifier for banner block contents.
     *
     * @return string
     */
    protected function _getCacheId()
    {
        if (!$this->_bannerCanBeCached()) {
            return false;
        }

        return 'CONTAINER_ENTERPRISE_BANNER_'
            . md5($this->_placeholder->getAttribute('cache_id')
            . $this->_getIdentifier());
    }

    /**
     * Generate placeholder content before application was initialized and apply to page content if possible.
     * We can cache only several types of banner settings, so check cache ability first.
     *
     * @param string $content
     * @return bool
     */
    public function applyWithoutApp(&$content)
    {
        if (!$this->_bannerCanBeCached()) {
            return false;
        }

        return parent::applyWithoutApp($content);
    }

    /**
     * Render banner block content
     *
     * @return string
     */
    protected function _renderBlock()
    {
        $placeholder = $this->_placeholder;
        $block = $placeholder->getAttribute('block');
        $block = new $block;

        $parameters = array('name', 'types', 'display_mode', 'rotate', 'banner_ids', 'unique_id');
        foreach ($parameters as $parameter) {
            $value = $placeholder->getAttribute($parameter);
            $block->setData($parameter, $value);
        }

        return $block->setTemplate($placeholder->getAttribute('template'))
            ->toHtml();
    }
}
