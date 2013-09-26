<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Backend
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Backend menu block
 *
 * @method Magento_Backend_Block_Menu setAdditionalCacheKeyInfo(array $cacheKeyInfo)
 * @method array getAdditionalCacheKeyInfo()
 */
class Magento_Backend_Block_Menu extends Magento_Backend_Block_Template
{
    const CACHE_TAGS = 'BACKEND_MAINMENU';

    /**
     * @var string
     */
    protected $_containerRenderer;

    /**
     * @var string
     */
    protected $_itemRenderer;

    /**
     * Backend URL instance
     *
     * @var Magento_Backend_Model_Url
     */
    protected $_url;

    /**
     * Current selected item
     *
     * @var Magento_Backend_Model_Menu_Item|null|bool
     */
    protected $_activeItemModel = null;

    /**
     * @var Magento_Backend_Model_Menu_Filter_IteratorFactory
     */
    protected $_iteratorFactory;

    /**
     * @var Magento_Backend_Model_Auth_Session
     */
    protected $_authSession;

    /**
     * @var Magento_Core_Model_LocaleInterface
     */
    protected $_locale;

    /**
     * @var Magento_Backend_Model_Menu_Config
     */
    protected $_menuConfig;

    public function __construct(
        Magento_Core_Helper_Data $coreData,
        Magento_Backend_Block_Template_Context $context,
        Magento_Backend_Model_Url $url,
        Magento_Backend_Model_Menu_Filter_IteratorFactory $iteratorFactory,
        Magento_Backend_Model_Auth_Session $authSession,
        Magento_Core_Model_LocaleInterface $locale,
        Magento_Backend_Model_Menu_Config $menuConfig,
        array $data = array()
    ) {
        $this->_url = $url;
        $this->_iteratorFactory = $iteratorFactory;
        $this->_authSession = $authSession;
        $this->_locale = $locale;
        $this->_menuConfig = $menuConfig;
        parent::__construct($coreData, $context, $data);
    }

    /**
     * Initialize template and cache settings
     */
    protected function _construct()
    {
        parent::_construct();
        $this->setCacheTags(array(self::CACHE_TAGS));
    }

    /**
     * Check whether given item is currently selected
     *
     * @param Magento_Backend_Model_Menu_Item $item
     * @param int $level
     * @return bool
     */
    protected function _isItemActive(Magento_Backend_Model_Menu_Item $item, $level)
    {
        $itemModel = $this->getActiveItemModel();
        $output = false;

        if ($level == 0
            && $itemModel instanceof Magento_Backend_Model_Menu_Item
            && ($itemModel->getId() == $item->getId()
                || $item->getChildren()->get($itemModel->getId())!== null)
        ) {
            $output = true;
        }
        return $output;
    }

    /**
     * Render menu item anchor label
     *
     * @param Magento_Backend_Model_Menu_Item $menuItem
     * @return string
     */
    protected function _getAnchorLabel($menuItem)
    {
        return $this->escapeHtml(__($menuItem->getTitle()));
    }

    /**
     * Render menu item anchor title
     *
     * @param Magento_Backend_Model_Menu_Item $menuItem
     * @return string
     */
    protected function _renderItemAnchorTitle($menuItem)
    {
        return $menuItem->hasTooltip() ? 'title="' . __($menuItem->getTooltip()) . '"' : '';
    }

    /**
     * Render menu item onclick function
     *
     * @param Magento_Backend_Model_Menu_Item $menuItem
     * @return string
     */
    protected function _renderItemOnclickFunction($menuItem)
    {
        return $menuItem->hasClickCallback() ? ' onclick="' . $menuItem->getClickCallback() . '"' : '';
    }

    /**
     * Render menu item anchor css class
     *
     * @param Magento_Backend_Model_Menu_Item $menuItem
     * @param int $level
     * @return string
     */
    protected function _renderAnchorCssClass($menuItem, $level)
    {
        return $this->_isItemActive($menuItem, $level) ? 'active' : '';
    }

    /**
     * Render menu item mouse events
     * @param Magento_Backend_Model_Menu_Item $menuItem
     * @return string
     */
    protected function _renderMouseEvent($menuItem)
    {
        return $menuItem->hasChildren() ?
            'onmouseover="Element.addClassName(this,\'over\')" onmouseout="Element.removeClassName(this,\'over\')"' :
            '';
    }

    /**
     * Render item css class
     *
     * @param Magento_Backend_Model_Menu_Item $menuItem
     * @param int $level
     * @return string
     */
    protected function _renderItemCssClass($menuItem, $level)
    {
        $isLast = 0 == $level && (bool) $this->getMenuModel()->isLast($menuItem) ? 'last' : '';
        $output = ($this->_isItemActive($menuItem, $level) ? 'active' : '')
            . ' ' . ($menuItem->hasChildren() ? 'parent' : '')
            . ' ' . $isLast
            . ' ' . 'level-' . $level;
        return $output;
    }

    /**
     * Render menu item anchor
     * @param Magento_Backend_Model_Menu_Item $menuItem
     * @param int $level
     * @return string
     */
    protected function _renderAnchor($menuItem, $level)
    {
        return '<a href="' . $menuItem->getUrl() . '" ' . $this->_renderItemAnchorTitle($menuItem)
            . $this->_renderItemOnclickFunction($menuItem)
            . ' class="' . $this->_renderAnchorCssClass($menuItem, $level) . '">'
            . '<span>' . $this->_getAnchorLabel($menuItem) . '</span>'
            . '</a>';
    }

    /**
     * Get menu filter iterator
     *
     * @param Magento_Backend_Model_Menu $menu
     * @return Magento_Backend_Model_Menu_Filter_Iterator
     */
    protected function _getMenuIterator($menu)
    {
        return $this->_iteratorFactory->create(array('iterator' => $menu->getIterator()));
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
            '#' . Magento_Backend_Model_Url::SECRET_KEY_PARAM_NAME . '/\$([^\/].*)/([^\/].*)/([^\$].*)\$#U',
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
        return Magento_Backend_Model_Url::SECRET_KEY_PARAM_NAME . '/'
            . $this->_url->getSecretKey($match[1], $match[2], $match[3]);
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
            $this->_authSession->getUser()->getId(),
            $this->_locale->getLocaleCode()
        );
        // Add additional key parameters if needed
        $newCacheKeyInfo = $this->getAdditionalCacheKeyInfo();
        if (is_array($newCacheKeyInfo) && !empty($newCacheKeyInfo)) {
            $cacheKeyInfo = array_merge($cacheKeyInfo, $newCacheKeyInfo);
        }
        return $cacheKeyInfo;
    }

    /**
     * Get menu config model
     *
     * @return Magento_Backend_Model_Menu
     */
    public function getMenuModel()
    {
        return $this->_menuConfig->getMenu();
    }

    /**
     * Render menu
     *
     * @param Magento_Backend_Model_Menu $menu
     * @param int $level
     * @return string HTML
     */
    public function renderMenu($menu, $level = 0)
    {
        $output = '<ul ' . (0 == $level ? 'id="nav"' : '') . ' >';

        /** @var $menuItem Magento_Backend_Model_Menu_Item  */
        foreach ($this->_getMenuIterator($menu) as $menuItem) {
            $output .= '<li ' . $this->_renderMouseEvent($menuItem)
                . ' class="' . $this->_renderItemCssClass($menuItem, $level) . '"'
                . $this->getUiId($menuItem->getId()) . '>';

            $output .= $this->_renderAnchor($menuItem, $level);

            if ($menuItem->hasChildren()) {
                $output .= $this->renderMenu($menuItem->getChildren(), $level + 1);
            }
            $output .='</li>';
        }
        $output .= '</ul>';

        return $output;
    }

    /**
     * Count All Subnavigation Items
     *
     * @param Magento_Backend_Model_Menu $items
     * @return int
     */
    protected function _countItems($items)
    {
        $total = count($items);
        foreach ($items as $item) {
            /** @var $item Magento_Backend_Model_Menu_Item */
            if ($item->hasChildren()) {
                $total += $this->_countItems($item->getChildren());
            }
        }
        return $total;
    }

    /**
     * Building Array with Column Brake Stops
     *
     * @param Magento_Backend_Model_Menu $items
     * @param int $limit
     * @return array
     * @todo: Add Depth Level limit, and better logic for columns
     */
    protected function _columnBrake($items, $limit)
    {
        $total = $this->_countItems($items);
        if ($total <= $limit) {
            return;
        }
        $result[] = array(
                'total' => $total,
                'max'   => ceil($total / ceil($total / $limit))
            );
        $count = 0;
        foreach ($items as $item) {
            $place = $this->_countItems($item->getChildren()) + 1;
            $count += $place;
            if ($place - $result[0]['max'] > $limit - $result[0]['max']) {
                $colbrake = true;
                $count = 0;
            } elseif ($count - $result[0]['max'] > $limit - $result[0]['max']) {
                $colbrake = true;
                $count = $place;
            } else {
                $colbrake = false;
            }
            $result[] = array(
                'place' => $place,
                'colbrake' => $colbrake
            );
        }
        return $result;
    }

    /**
     * Add sub menu HTML code for current menu item
     *
     * @param $menuItem Magento_Backend_Model_Menu_Item
     * @param $level int
     * @param $limit int
     * @return string HTML code
     */
    protected function _addSubMenu($menuItem, $level, $limit)
    {
        $output = '';
        if (!$menuItem->hasChildren()) {
            return $output;
        }
        $output .= '<div class="submenu">';
        $colStops = null;
        if ($level == 0 && $limit) {
            $colStops = $this->_columnBrake($menuItem->getChildren(), $limit);
        }
        $output .= $this->renderNavigation($menuItem->getChildren(), $level + 1, $limit, $colStops);
        $output .= '</div>';
        return $output;
    }

    /**
     * Render Navigation
     *
     * @param Magento_Backend_Model_Menu $menu
     * @param int $level
     * @param int $limit
     * @param array $colBrakes
     * @return string HTML
     */
    public function renderNavigation($menu, $level = 0, $limit = 0, $colBrakes = array())
    {
        $itemPosition = 1;
        $outputStart = '<ul ' . (0 == $level ? 'id="nav"' : '') . ' >';
        $output = '';

        /** @var $menuItem Magento_Backend_Model_Menu_Item  */
        foreach ($this->_getMenuIterator($menu) as $menuItem) {
            $menuId = $menuItem->getId();
            $itemName = substr($menuId, strrpos($menuId, '::') + 2);
            $itemClass = str_replace('_', '-', strtolower($itemName));

            if (count($colBrakes) && $colBrakes[$itemPosition]['colbrake']) {
                $output .= '</ul></li><li class="column"><ul>';
            }

            $output .= '<li ' . $this->getUiId($menuItem->getId())
                . ' class="item-' . $itemClass . ' '
                . $this->_renderItemCssClass($menuItem, $level) . '">'
                . $this->_renderAnchor($menuItem, $level)
                . $this->_addSubMenu($menuItem, $level, $limit)
                . '</li>';
            $itemPosition++;
        }

        if (count($colBrakes) && $limit) {
            $output = '<li class="column"><ul>' . $output . '</ul></li>';
        }

        return $outputStart . $output . '</ul>';;
    }

    /**
     * Get current selected menu item
     *
     * @return Magento_Backend_Model_Menu_Item|null|bool
     */
    public function getActiveItemModel()
    {
        if (is_null($this->_activeItemModel)) {
            $this->_activeItemModel = $this->getMenuModel()->get($this->getActive());
            if (false == ($this->_activeItemModel instanceof Magento_Backend_Model_Menu_Item)) {
                $this->_activeItemModel = false;
            }
        }
        return $this->_activeItemModel;
    }
}
