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
     * Retrieve Title value for menu node
     *
     * @param Varien_Simplexml_Element $child
     * @return string
     */
    protected function _getHelperValue(Varien_Simplexml_Element $child)
    {
        $helperName         = 'Mage_Backend_Helper_Data';
        $titleNodeName      = 'title';
        $childAttributes    = $child->attributes();
        if (isset($childAttributes['module'])) {
            $helperName     = (string)$childAttributes['module'];
        }
//        if (isset($childAttributes['translate'])) {
//            $titleNodeName  = (string)$childAttributes['translate'];
//        }

        return Mage::helper($helperName)->__((string)$child->$titleNodeName);
    }

    /**
     * Processing block html after rendering
     *
     * @param   string $html
     * @return  string
     */
    protected function _afterToHtml($html)
    {
        $html = preg_replace_callback('#'.Mage_Backend_Model_Url::SECRET_KEY_PARAM_NAME.'/\$([^\/].*)/([^\$].*)\$#U', array($this, '_callbackSecretKey'), $html);

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
     * Render HTML menu recursively starting from the specified level
     *
     * @deprecated ?
     * @param array $menu
     * @param int $level
     * @return string
     */
    protected function _renderMenuLevel(array $menu, $level = 0)
    {
        $result = '<ul' . (!$level ? ' id="nav"' : '') . '>';
        foreach ($menu as $item) {
            $hasChildren = !empty($item['children']);
            $cssClasses = array('level' . $level);
            if (!$level && !empty($item['active'])) {
                $cssClasses[] = 'active';
            }
            if ($hasChildren) {
                $cssClasses[] = 'parent';
            }
            if (!empty($level) && !empty($item['last'])) {
                $cssClasses[] = 'last';
            }
            $result .= '<li'
                . ($hasChildren ? ' onmouseover="Element.addClassName(this,\'over\')"' : '')
                . ($hasChildren ? ' onmouseout="Element.removeClassName(this,\'over\')"' : '')
                . ' class="' . implode(' ', $cssClasses) . '">'
                . '<a'
                . ' href="' . $item['url'] . '"'
                . (!empty($item['title']) ? ' title="' . $item['title'] . '"' : '')
                . (!empty($item['click']) ? ' onclick="' . $item['click'] . '"' : '')
                . ($level === 0 && !empty($item['active']) ? ' class="active"' : '')
                . '>'
                . '<span>' . Mage::helper('Mage_Backend_Helper_Data')->escapeHtml($item['label']) . '</span>'
                . '</a>'
            ;
            if ($hasChildren) {
                $result .= $this->_renderMenuLevel($item['children'], $level + 1);
            }
            $result .= '</li>';
        }
        $result .= '</ul>';
        return $result;
    }

    /**
     * Render HTML menu
     * @deprecated ?
     * @return string
     */
    public function renderMenu()
    {
        return $this->_renderMenuLevel($this->_buildMenuArray());
    }

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
    public function getMenuLevel(Mage_Backend_Model_Menu $menu, $level = 0)
    {
        $html = '<ul ' . (!$level ? 'id="nav"' : '') . '>' . PHP_EOL;
        foreach ($menu as $item) {
            $html .= '<li ' . ($item->hasChildren() ? 'onmouseover="Element.addClassName(this,\'over\')" '
                . 'onmouseout="Element.removeClassName(this,\'over\')"' : '') . ' class="'
                . (!$level && $this->_isItemActive($item) ? ' active' : '') . ' '
                . ($item->hasChildren() ? ' parent' : '')
                . (!empty($level) && $menu->isLast($item) ? ' last' : '')
                . ' level' . $level . '"> <a href="' . $item->getUrl() . '" '
                . ($item->hasTitle() ? 'title="' . $item->getTitle() . '"' : '') . ' '
                . ($item->hasClickCallback() ? 'onclick="' . $item->getClickCallback() . '"' : '') . ' class="'
                . ($level === 0 && $item->isActive() ? 'active' : '') . '"><span>'
                . $this->escapeHtml($item->getLabel()) . '</span></a>' . PHP_EOL;

            if ($item->hasChildren()) {
                $html .= $this->getMenuLevel($item->getChildren(), $level + 1);
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
        return ($this->getActive() == $item->getPath())
            || (strpos($this->getActive(), $item->getPath().'/')===0);
    }
}
