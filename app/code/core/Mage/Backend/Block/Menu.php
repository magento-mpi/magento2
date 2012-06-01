<?php
/**
 * {license_notice}
 *
 * @category    Mage
 * @package     Mage_Backend
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Backend menu block
 *
 * @method Mage_Backend_Block_Menu setAdditionalCacheKeyInfo(array $cacheKeyInfo)
 * @method array getAdditionalCacheKeyInfo()
 *
 * @category   Mage
 * @package    Mage_Backend
 * @author     Magento Core Team <core@magentocommerce.com>
 */
class Mage_Backend_Block_Menu extends Mage_Backend_Block_Template
{
    const CACHE_TAGS = 'BACKEND_MAINMENU';

    /**
     * Backend URL instance
     *
     * @var Mage_Backend_Model_Url
     */
    protected $_url;

    /**
     * Initialize template and cache settings
     *
     */
    protected function _construct()
    {
        parent::_construct();
        $this->setTemplate('Mage_Backend::menu.phtml');
        $this->_url = Mage::getModel('Mage_Backend_Model_Url');
        $this->setCacheTags(array(self::CACHE_TAGS));
    }

    /**
     * Retrieve cache lifetime
     *
     * @return int
     */
    public function getCacheLifetime()
    {
        return 86400;
    }

    /**
     * Get Key pieces for caching block content
     *
     * @return array
     */
    public function getCacheKeyInfo()
    {
        $cacheKeyInfo = array(
            'admin_top_nav',
            $this->getActive(),
            Mage::getSingleton('Mage_Backend_Model_Auth_Session')->getUser()->getId(),
            Mage::app()->getLocale()->getLocaleCode()
        );
        // Add additional key parameters if needed
        $additionalCacheKeyInfo = $this->getAdditionalCacheKeyInfo();
        if (is_array($additionalCacheKeyInfo) && !empty($additionalCacheKeyInfo)) {
            $cacheKeyInfo = array_merge($cacheKeyInfo, $additionalCacheKeyInfo);
        }
        return $cacheKeyInfo;
    }

    /**
     * Processing block html after rendering
     *
     * @param   string $html
     * @return  string
     */
    protected function _afterToHtml($html)
    {
        $html = preg_replace_callback(
            '#'.Mage_Backend_Model_Url::SECRET_KEY_PARAM_NAME.'/\$([^\/].*)/([^\$].*)\$#U',
            array($this, '_callbackSecretKey'),
            $html
        );

        return $html;
    }

    /**
     * Replace Callback Secret Key
     *
     * @param array $match
     * @return string
     */
    protected function _callbackSecretKey($match)
    {
        return Mage_Backend_Model_Url::SECRET_KEY_PARAM_NAME . '/'
            . $this->_url->getSecretKey($match[1], $match[2]);
    }

    /**
     * Get menu config model
     * @return Mage_Backend_Model_Menu
     */
    public function getMenuModel()
    {
        return Mage::getSingleton('Mage_Backend_Model_Menu_Config')->getMenu();
    }

    /**
     * Get menu level HTML code
     *
     * @param Mage_Backend_Model_Menu $menu
     * @param int $level
     * @return string
     */
    public function getMenuLevelHtml(Mage_Backend_Model_Menu $menu, $level = 0)
    {
        $html = '<ul ' . (!$level ? 'id="nav"' : '') . '>' . PHP_EOL;
        foreach ($menu as $item) {
            $html .= '<li ' . ($item->hasChildren() ? 'onmouseover="Element.addClassName(this,\'over\')" '
                . 'onmouseout="Element.removeClassName(this,\'over\')"' : '') . ' class="'
                . (!$level && $this->_isItemActive($item) ? ' active' : '') . ' '
                . ($item->hasChildren() ? ' parent' : '')
                . (!empty($level) && $menu->isLast($item) ? ' last' : '')
                . ' level' . $level . '"> <a href="' . $item->getUrl() . '" '
                . ($item->hasTooltip() ? 'title="' . $item->getModuleHelper()->__($item->getTooltip()) . '"' : '') . ' '
                . ($item->hasClickCallback() ? 'onclick="' . $item->getClickCallback() . '"' : '') . ' class="'
                . ($level === 0 && $this->_isItemActive($item) ? 'active' : '') . '"><span>'
                . $this->escapeHtml($item->getModuleHelper()->__($item->getTitle())) . '</span></a>' . PHP_EOL;

            if ($item->hasChildren()) {
                $html .= $this->getMenuLevelHtml($item->getChildren(), $level + 1);
            }
            $html .= '</li>' . PHP_EOL;
        }
        $html .= '</ul>' . PHP_EOL;

        return $html;
    }

    /**
     * Check whether given item is currently selected
     *
     * @param Mage_Backend_Model_Menu_Item $item
     * @return bool
     */
    public function _isItemActive(Mage_Backend_Model_Menu_Item $item)
    {
        return ($this->getActive() == $item->getFullPath())
            || (strpos($this->getActive(), $item->getFullPath().'/')===0);
    }
}
