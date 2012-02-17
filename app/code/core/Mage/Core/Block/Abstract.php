<?php
/**
 * {license_notice}
 *
 * @category    Mage
 * @package     Mage_Core
 * @copyright   {copyright}
 * @license     {license_link}
 */


/**
 * Base Content Block class
 *
 * For block generation you must define Data source class, data source class method,
 * parameters array and block template
 *
 * @category   Mage
 * @package    Mage_Core
 * @author      Magento Core Team <core@magentocommerce.com>
 * @SuppressWarnings (PHPMD.ExcessivePublicCount)
 */
abstract class Mage_Core_Block_Abstract extends Varien_Object
{
    /**
     * Cache group Tag
     */
    const CACHE_GROUP = 'block_html';
    /**
     * Block name in layout
     *
     * @var string
     */
    protected $_nameInLayout;

    /**
     * Parent layout of the block
     *
     * @var Mage_Core_Model_Layout
     */
    protected $_layout;

    /**
     * Short alias of this block that was refered from parent
     *
     * @var string
     */
    protected $_alias;

    /**
     * Suffix for name of anonymous block
     *
     * @var string
     */
    protected $_anonSuffix;

    /**
     * Children blocks HTML cache array
     *
     * @var array
     */
    protected $_childrenHtmlCache           = array();

    /**
     * Arbitrary groups of child blocks
     *
     * @var array
     */
    protected $_childGroups                 = array();

    /**
     * Request object
     *
     * @var Zend_Controller_Request_Http
     */
    protected $_request;

    /**
     * Messages block instance
     *
     * @var Mage_Core_Block_Messages
     */
    protected $_messagesBlock               = null;

    /**
     * Whether this block was not explicitly named
     *
     * @var boolean
     */
    protected $_isAnonymous                 = false;

    /**
     * Block html frame open tag
     * @var string
     */
    protected $_frameOpenTag;

    /**
     * Block html frame close tag
     * @var string
     */
    protected $_frameCloseTag;

    /**
     * Url object
     *
     * @var Mage_Core_Model_Url
     */
    protected static $_urlModel;

    /**
     * @var Varien_Object
     */
    private static $_transportObject;

    /**
     * Array of block sort priority instructions
     *
     * @var array
     */
    protected $_sortInstructions = array();

    /**
     * Constructor
     */
    public function __construct(array $data= array())
    {
        parent::__construct($data);
        $this->_construct();

    }

    /**
     * Internal constructor, that is called from real constructor
     *
     * Please override this one instead of overriding real __construct constructor
     *
     */
    protected function _construct()
    {
        /**
         * Please override this one instead of overriding real __construct constructor
         */
    }

    /**
     * Retrieve request object
     *
     * @return Mage_Core_Controller_Request_Http
     * @throws Exception
     */
    public function getRequest()
    {
        $controller = Mage::app()->getFrontController();
        if ($controller) {
            $this->_request = $controller->getRequest();
        } else {
            throw new Exception(Mage::helper('Mage_Core_Helper_Data')->__("Can't retrieve request object"));
        }
        return $this->_request;
    }

    /**
     * Retrieve parent block
     *
     * @return Mage_Core_Block_Abstract
     */
    public function getParentBlock()
    {
        return $this->_getLayoutStructure()->getParentElement($this->getNameInLayout());
    }

    /**
     * Set parent block
     *
     * @param   Mage_Core_Block_Abstract $block
     * @return  Mage_Core_Block_Abstract
     * @supressWarnings(PHPMD.UnusedFormalParameter)
     * @deprecated
     */
    public function setParentBlock(Mage_Core_Block_Abstract $block)
    {
        return $this;
    }

    /**
     * Retrieve current action object
     *
     * @return Mage_Core_Controller_Varien_Action
     */
    public function getAction()
    {
        return Mage::app()->getFrontController()->getAction();
    }

    /**
     * Set layout object
     *
     * @param   Mage_Core_Model_Layout $layout
     * @return  Mage_Core_Block_Abstract
     */
    public function setLayout(Mage_Core_Model_Layout $layout)
    {
        $this->_layout = $layout;
        Mage::dispatchEvent('core_block_abstract_prepare_layout_before', array('block' => $this));
        $this->_prepareLayout();
        Mage::dispatchEvent('core_block_abstract_prepare_layout_after', array('block' => $this));
        return $this;
    }

    /**
     * Preparing global layout
     *
     * You can redefine this method in child classes for changing layout
     *
     * @return Mage_Core_Block_Abstract
     */
    protected function _prepareLayout()
    {
        return $this;
    }

    /**
     * Retrieve layout object
     *
     * @return Mage_Core_Model_Layout
     */
    public function getLayout()
    {
        return $this->_layout;
    }

    /**
     * Check if block is using auto generated (Anonymous) name
     * @return bool
     */
    public function isAnonymous()
    {
        return $this->_isAnonymous;
    }

    /**
     * Set the anonymous flag
     *
     * @param  bool $flag
     * @return Mage_Core_Block_Abstract
     */
    public function setIsAnonymous($flag)
    {
        $this->_isAnonymous = (bool)$flag;
        return $this;
    }

    /**
     * Returns anonymous block suffix
     *
     * @return string
     */
    public function getAnonSuffix()
    {
        return $this->_anonSuffix;
    }

    /**
     * Set anonymous suffix for current block
     *
     * @param string $suffix
     * @return Mage_Core_Block_Abstract
     */
    public function setAnonSuffix($suffix)
    {
        $this->_anonSuffix = $suffix;
        return $this;
    }

    /**
     * Returns block alias
     *
     * @return string
     */
    public function getBlockAlias()
    {
        return $this->_alias;
    }

    /**
     * Set block alias
     *
     * @param string $alias
     * @return Mage_Core_Block_Abstract
     */
    public function setBlockAlias($alias)
    {
        $this->_alias = $alias;
        return $this;
    }

    /**
     * Set block's name in layout and unsets previous link if such exists.
     *
     * @param string $name
     * @return Mage_Core_Block_Abstract
     */
    public function setNameInLayout($name)
    {
        if (!empty($this->_nameInLayout) && $this->getLayout()) {
            $this->getLayout()->unsetBlock($this->_nameInLayout)
                ->setBlock($name, $this);
        }
        $this->_nameInLayout = $name;
        return $this;
    }

    /**
     * Retrieve sorted list of children.
     *
     * @return array
     */
    public function getSortedChildren()
    {
        return $this->_getLayoutStructure()->getSortedChildren($this->getNameInLayout());
    }

    /**
     * Set block attribute value
     *
     * Wrapper for method "setData"
     *
     * @param   string $name
     * @param   mixed $value
     * @return  Mage_Core_Block_Abstract
     */
    public function setAttribute($name, $value = null)
    {
        return $this->setData($name, $value);
    }

    /**
     * Set child block
     *
     * @param   string $alias
     * @param   Mage_Core_Block_Abstract $block
     * @return  Mage_Core_Block_Abstract
     */
    public function setChild($alias, $block)
    {
        if (!is_string($block)) {
            $block = $block->getNameInLayout();
        }
        $this->_getLayoutStructure()->setChild($this->getNameInLayout(), $block, $alias);
        return $this;
    }

    /**
     * Unset child block
     *
     * @param  string $alias
     * @return Mage_Core_Block_Abstract
     */
    public function unsetChild($alias)
    {
        $this->_getLayoutStructure()->unsetChild($this->getNameInLayout(), $alias);
        return $this;
    }

    /**
     * Call a child and unset it, if callback matched result
     *
     * $params will pass to child callback
     * $params may be array, if called from layout with elements with same name, for example:
     * ...<foo>value_1</foo><foo>value_2</foo><foo>value_3</foo>
     *
     * Or, if called like this:
     * ...<foo>value_1</foo><bar>value_2</bar><baz>value_3</baz>
     * - then it will be $params1, $params2, $params3
     *
     * It is no difference anyway, because they will be transformed in appropriate way.
     *
     * @param string $alias
     * @param string $callback
     * @param mixed $result
     * @param array $params
     * @return Mage_Core_Block_Abstract
     */
    public function unsetCallChild($alias, $callback, $result, $params)
    {
        $this->_getLayoutStructure()->unsetCallChild($this->getNameInLayout(), $alias, $callback, $result, $params);
        return $this;
    }

    /**
     * Unset all children blocks
     *
     * @return Mage_Core_Block_Abstract
     */
    public function unsetChildren()
    {
        $this->_getLayoutStructure()->unsetChildren($this->getNameInLayout());
        return $this;
    }

    /**
     * Retrieve child block by name
     *
     * @param string $alias
     * @return array|Mage_Core_Block_Abstract
     */
    public function getChild($alias = '')
    {
        return $this->_getLayoutStructure()->getChild($this->getNameInLayout(), $alias);
    }

    /**
     * Retrieve child block HTML
     *
     * @param   string $name
     * @param   boolean $useCache
     * @param   boolean $sorted
     * @return  string
     */
    public function getChildHtml($name = '', $useCache = true, $sorted = false)
    {
        if ($name === '') {
            $children = $this->getSortedChildren();
            $out = '';
            foreach ($children as $child) {
                $out .= $this->_getChildHtml($child, $useCache);
            }
            return $out;
        } else {
            return $this->_getChildHtml($name, $useCache);
        }
    }

    /**
     * @param string $name          Parent block name
     * @param string $childName     OPTIONAL Child block name
     * @param bool $useCache        OPTIONAL Use cache flag
     * @param bool $sorted          OPTIONAL @see getChildHtml()
     * @return string
     */
    public function getChildChildHtml($name, $childName = '', $useCache = true, $sorted = false)
    {
        if (empty($name)) {
            return '';
        }
        $child = $this->getChild($name);
        if (!$child) {
            return '';
        }
        return $child->getChildHtml($childName, $useCache, $sorted);
    }

    /**
     * Obtain sorted child blocks
     *
     * @return array
     */
    public function getSortedChildBlocks()
    {
        $elements = $this->_getLayoutStructure()->getSortedChildrenElements($this->getNameInLayout());
        foreach ($elements as $k => $element) {
            if (!$this->getLayout()->getStructure()->isBlock($element)) {
                unset($elements[$k]);
            }
        }
        return $elements;
    }

    /**
     * Retrieve child block HTML
     *
     * @param   string $name
     * @param   boolean $useCache
     * @return  string
     */
    protected function _getChildHtml($name, $useCache = true)
    {
        if ($useCache && isset($this->_childrenHtmlCache[$name])) {
            return $this->_childrenHtmlCache[$name];
        }

        $this->_childrenHtmlCache[$name] = $this->getLayout()->getChildHtml($this->getNameInLayout(), $name);
        return $this->_childrenHtmlCache[$name];
    }

    /**
     * Prepare child block before generate html
     *
     * @param   string $name
     * @param   Mage_Core_Block_Abstract $child
     */
    protected function _beforeChildToHtml($name, $child)
    {
    }

    /**
     * Retrieve block html
     *
     * @param   string $name
     * @return  string
     */
    public function getBlockHtml($name)
    {
        return $this->_getLayoutStructure()->getElementHtml($name);
    }

    /**
     * Make sure specified block will be registered in the specified child groups
     *
     * @param string $groupName
     * @param Mage_Core_Block_Abstract $child
     */
    public function addToChildGroup($groupName, Mage_Core_Block_Abstract $child)
    {
        // TODO: refactor
        if (!isset($this->_childGroups[$groupName])) {
            $this->_childGroups[$groupName] = array();
        }
        if (!in_array($child->getBlockAlias(), $this->_childGroups[$groupName])) {
            $this->_childGroups[$groupName][] = $child->getBlockAlias();
        }
    }

    /**
     * @param Mage_Core_Block_Abstract|string $block
     * @param string $alias
     * @return Mage_Core_Block_Abstract
     */
    public function append($block, $alias = '')
    {
        if ($block instanceof Mage_Core_Block_Abstract) {
            $block->getNameInLayout();
        }
        // TODO: remove it after addopting layout
        $this->_getLayoutStructure()
            ->insertElement($this->getNameInLayout(), $block, 'block', $alias);
        return $this;
    }

    /**
     * Add self to the specified group of parent block
     *
     * @param string $groupName
     * @return Mage_Core_Block_Abstract
     */
    public function addToParentGroup($groupName)
    {
        // TODO: refactor
        $this->getParentBlock()->addToChildGroup($groupName, $this);
        return $this;
    }

    /**
     * Get a group of child blocks
     *
     * Returns an array of <alias> => <block>
     * or an array of <alias> => <callback_result>
     * The callback currently supports only $this methods and passes the alias as parameter
     *
     * @param string $groupName
     * @param string $callback
     * @param bool $skipEmptyResults
     * @return array
     */
    public function getChildGroup($groupName, $callback = null, $skipEmptyResults = true)
    {
        // TODO: refactor
        $result = array();
        if (!isset($this->_childGroups[$groupName])) {
            return $result;
        }
        foreach ($this->getSortedChildBlocks() as $block) {
            if (Mage_Core_Model_Layout_Structure::ELEMENT_TYPE_BLOCK !== $block) {
                continue;
            }
            $alias = $block['alias'];
            if (in_array($alias, $this->_childGroups[$groupName])) {
                if ($callback) {
                    $row = $this->$callback($alias);
                    if (!$skipEmptyResults || $row) {
                        $result[$alias] = $row;
                    }
                } else {
                    $result[$alias] = $this->getLayout()->getBlock($block['name']);
                }

            }
        }
        return $result;
    }

    /**
     * Get a value from child block by specified key
     *
     * @param string $alias
     * @param string $key
     * @return mixed
     */
    public function getChildData($alias, $key = '')
    {
        $child = $this->getChild($alias);
        if ($child) {
            return $child->getData($key);
        }
        return false;
    }

    /**
     * Before rendering html, but after trying to load cache
     *
     * @return Mage_Core_Block_Abstract
     */
    protected function _beforeToHtml()
    {
        return $this;
    }

    /**
     * Specify block output frame tags
     *
     * @param $openTag
     * @param $closeTag
     * @return Mage_Core_Block_Abstract
     */
    public function setFrameTags($openTag, $closeTag = null)
    {
        $this->_frameOpenTag = $openTag;
        if ($closeTag) {
            $this->_frameCloseTag = $closeTag;
        } else {
            $this->_frameCloseTag = '/' . $openTag;
        }
        return $this;
    }

    /**
     * Produce and return block's html output
     *
     * It is a final method, but you can override _toHtml() method in descendants if needed.
     *
     * @return string
     */
    final public function toHtml()
    {
        Mage::dispatchEvent('core_block_abstract_to_html_before', array('block' => $this));
        if (Mage::getStoreConfig('advanced/modules_disable_output/' . $this->getModuleName())) {
            return '';
        }
        $html = $this->_loadCache();
        if ($html === false) {
            $translate = Mage::getSingleton('Mage_Core_Model_Translate');
            /** @var $translate Mage_Core_Model_Translate */
            if ($this->hasData('translate_inline')) {
                $translate->setTranslateInline($this->getData('translate_inline'));
            }

            $this->_beforeToHtml();
            $html = $this->_toHtml();
            $this->_saveCache($html);

            if ($this->hasData('translate_inline')) {
                $translate->setTranslateInline(true);
            }
        }
        $html = $this->_afterToHtml($html);

        /**
         * Check framing options
         */
        if ($this->_frameOpenTag) {
            $html = '<'.$this->_frameOpenTag.'>'.$html.'<'.$this->_frameCloseTag.'>';
        }

        /**
         * Use single transport object instance for all blocks
         */
        if (self::$_transportObject === null) {
            self::$_transportObject = new Varien_Object;
        }
        self::$_transportObject->setHtml($html);
        Mage::dispatchEvent('core_block_abstract_to_html_after',
                array('block' => $this, 'transport' => self::$_transportObject));
        $html = self::$_transportObject->getHtml();

        return $html;
    }

    /**
     * Processing block html after rendering
     *
     * @param   string $html
     * @return  string
     */
    protected function _afterToHtml($html)
    {
        return $html;
    }

    /**
     * Override this method in descendants to produce html
     *
     * @return string
     */
    protected function _toHtml()
    {
        return '';
    }

    /**
     * Returns url model class name
     *
     * @return string
     */
    protected function _getUrlModelClass()
    {
        return 'Mage_Core_Model_Url';
    }

    /**
     * Create and return url object
     *
     * @return Mage_Core_Model_Url
     */
    protected function _getUrlModel()
    {
        return Mage::getModel($this->_getUrlModelClass());
    }

    /**
     * Generate url by route and parameters
     *
     * @param   string $route
     * @param   array $params
     * @return  string
     */
    public function getUrl($route = '', $params = array())
    {
        return $this->_getUrlModel()->getUrl($route, $params);
    }

    /**
     * Generate base64-encoded url by route and parameters
     *
     * @param   string $route
     * @param   array $params
     * @return  string
     */
    public function getUrlBase64($route = '', $params = array())
    {
        return Mage::helper('Mage_Core_Helper_Data')->urlEncode($this->getUrl($route, $params));
    }

    /**
     * Generate url-encoded url by route and parameters
     *
     * @param   string $route
     * @param   array $params
     * @return  string
     */
    public function getUrlEncoded($route = '', $params = array())
    {
        return Mage::helper('Mage_Core_Helper_Data')->urlEncode($this->getUrl($route, $params));
    }

    /**
     * Retrieve url of skins file
     *
     * @param   string $file path to file in skin
     * @param   array $params
     * @return  string
     */
    public function getSkinUrl($file = null, array $params = array())
    {
        return Mage::getDesign()->getSkinUrl($file, $params);
    }

    /**
     * Retrieve messages block
     *
     * @return Mage_Core_Block_Messages
     */
    public function getMessagesBlock()
    {
        if (is_null($this->_messagesBlock)) {
            return $this->getLayout()->getMessagesBlock();
        }
        return $this->_messagesBlock;
    }

    /**
     * Set messages block
     *
     * @param   Mage_Core_Block_Messages $block
     * @return  Mage_Core_Block_Abstract
     */
    public function setMessagesBlock(Mage_Core_Block_Messages $block)
    {
        $this->_messagesBlock = $block;
        return $this;
    }

    /**
     * Return block helper
     *
     * @param string $type
     * @return Mage_Core_Block_Abstract
     */
    public function getHelper($type)
    {
        return $this->getLayout()->getBlockSingleton($type);
    }

    /**
     * Returns helper object
     *
     * @param string $name
     * @return Mage_Core_Block_Abstract
     */
    public function helper($name)
    {
        if ($this->getLayout()) {
            return $this->getLayout()->helper($name);
        }
        return Mage::helper($name);
    }

    /**
     * Retrieve formatting date
     *
     * @param   string $date
     * @param   string $format
     * @param   bool $showTime
     * @return  string
     */
    public function formatDate($date = null, $format =  Mage_Core_Model_Locale::FORMAT_TYPE_SHORT, $showTime = false)
    {
        return $this->helper('Mage_Core_Helper_Data')->formatDate($date, $format, $showTime);
    }

    /**
     * Retrieve formatting time
     *
     * @param   string $time
     * @param   string $format
     * @param   bool $showDate
     * @return  string
     */
    public function formatTime($time = null, $format =  Mage_Core_Model_Locale::FORMAT_TYPE_SHORT, $showDate = false)
    {
        return $this->helper('Mage_Core_Helper_Data')->formatTime($time, $format, $showDate);
    }

    /**
     * Retrieve module name of block
     *
     * @return string
     */
    public function getModuleName()
    {
        $module = $this->getData('module_name');
        if (is_null($module)) {
            $class = get_class($this);
            $module = substr($class, 0, strpos($class, '_Block'));
            $this->setData('module_name', $module);
        }
        return $module;
    }

    /**
     * Translate block sentence
     *
     * @return string
     * @assertWarnings(PHPMD.ShortMethodName)
     */
    public function __()
    {
        $args = func_get_args();
        $expr = new Mage_Core_Model_Translate_Expr(array_shift($args), $this->getModuleName());
        array_unshift($args, $expr);
        return Mage::app()->getTranslator()->translate($args);
    }

    /**
     * Escape html entities
     *
     * @param   string|array $data
     * @param   array $allowedTags
     * @return  string
     */
    public function escapeHtml($data, $allowedTags = null)
    {
        return $this->helper('Mage_Core_Helper_Data')->escapeHtml($data, $allowedTags);
    }

    /**
     * Wrapper for standard strip_tags() function with extra functionality for html entities
     *
     * @param string $data
     * @param string $allowableTags
     * @param bool $allowHtmlEntities
     * @return string
     */
    public function stripTags($data, $allowableTags = null, $allowHtmlEntities = false)
    {
        return $this->helper('Mage_Core_Helper_Data')->stripTags($data, $allowableTags, $allowHtmlEntities);
    }

    /**
     * Escape html entities in url
     *
     * @param string $data
     * @return string
     */
    public function escapeUrl($data)
    {
        return $this->helper('Mage_Core_Helper_Data')->escapeUrl($data);
    }

    /**
     * Escape quotes in java scripts
     *
     * @param mixed $data
     * @param string $quote
     * @return mixed
     */
    public function jsQuoteEscape($data, $quote = '\'')
    {
        return $this->helper('Mage_Core_Helper_Data')->jsQuoteEscape($data, $quote);
    }

    /**
     * Alias for getName method.
     *
     * @return string
     */
    public function getNameInLayout()
    {
        return $this->_nameInLayout;
    }

    /**
     * Get chilren blocks count
     * @return int
     */
    public function countChildren()
    {
        return $this->_getLayoutStructure()->getChildrenCount($this->getNameInLayout());
    }

    /**
     * Prepare url for save to cache
     *
     * @return Mage_Core_Block_Abstract
     */
    protected function _beforeCacheUrl()
    {
        if (Mage::app()->useCache(self::CACHE_GROUP)) {
            Mage::app()->setUseSessionVar(true);
        }
        return $this;
    }

    /**
     * Replace URLs from cache
     *
     * @param string $html
     * @return string
     */
    protected function _afterCacheUrl($html)
    {
        if (Mage::app()->useCache(self::CACHE_GROUP)) {
            Mage::app()->setUseSessionVar(false);
            Magento_Profiler::start('CACHE_URL');
            $html = Mage::getSingleton($this->_getUrlModelClass())->sessionUrlVar($html);
            Magento_Profiler::stop('CACHE_URL');
        }
        return $html;
    }

    /**
     * Get cache key informative items
     * Provide string array key to share specific info item with FPC placeholder
     *
     * @return array
     */
    public function getCacheKeyInfo()
    {
        return array(
            $this->getNameInLayout()
        );
    }

    /**
     * Get Key for caching block content
     *
     * @return string
     */
    public function getCacheKey()
    {
        if ($this->hasData('cache_key')) {
            return $this->getData('cache_key');
        }
        /**
         * don't prevent recalculation by saving generated cache key
         * because of ability to render single block instance with different data
         */
        $key = $this->getCacheKeyInfo();
        //ksort($key);  // ignore order
        $key = array_values($key);  // ignore array keys
        $key = implode('|', $key);
        $key = sha1($key);
        return $key;
    }

    /**
     * Get tags array for saving cache
     *
     * @return array
     */
    public function getCacheTags()
    {
        if (!$this->hasData('cache_tags')) {
            $tags = array();
        } else {
            $tags = $this->getData('cache_tags');
        }
        $tags[] = self::CACHE_GROUP;
        return $tags;
    }

    /**
     * Get block cache life time
     *
     * @return int
     */
    public function getCacheLifetime()
    {
        if (!$this->hasData('cache_lifetime')) {
            return null;
        }
        return $this->getData('cache_lifetime');
    }

    /**
     * Load block html from cache storage
     *
     * @return string | false
     */
    protected function _loadCache()
    {
        if (is_null($this->getCacheLifetime()) || !Mage::app()->useCache(self::CACHE_GROUP)) {
            return false;
        }
        $cacheKey = $this->getCacheKey();
        /** @var $session Mage_Core_Model_Session */
        $session = Mage::getSingleton('Mage_Core_Model_Session');
        $cacheData = Mage::app()->loadCache($cacheKey);
        if ($cacheData) {
            $cacheData = str_replace(
                $this->_getSidPlaceholder($cacheKey),
                $session->getSessionIdQueryParam() . '=' . $session->getEncryptedSessionId(),
                $cacheData
            );
        }
        return $cacheData;
    }

    /**
     * Save block content to cache storage
     *
     * @param string $data
     * @return Mage_Core_Block_Abstract
     */
    protected function _saveCache($data)
    {
        if (is_null($this->getCacheLifetime()) || !Mage::app()->useCache(self::CACHE_GROUP)) {
            return false;
        }
        $cacheKey = $this->getCacheKey();
        /** @var $session Mage_Core_Model_Session */
        $session = Mage::getSingleton('Mage_Core_Model_Session');
        $data = str_replace(
            $session->getSessionIdQueryParam() . '=' . $session->getEncryptedSessionId(),
            $this->_getSidPlaceholder($cacheKey),
            $data
        );

        Mage::app()->saveCache($data, $cacheKey, $this->getCacheTags(), $this->getCacheLifetime());
        return $this;
    }

    /**
     * Get SID placeholder for cache
     *
     * @param null|string $cacheKey
     * @return string
     */
    protected function _getSidPlaceholder($cacheKey = null)
    {
        if (is_null($cacheKey)) {
            $cacheKey = $this->getCacheKey();
        }

        return '<!--SID=' . $cacheKey . '-->';
    }

    /**
     * Get variable value from view configuration
     *
     * Module name can be omitted. If omitted, it will be determined automatically.
     *
     * @param string $name variable name
     * @param string $module optional module name
     * @return string|false
     */
    public function getVar($name, $module = null)
    {
        $module = $module ?: $this->getModuleName();
        return Mage::getDesign()->getViewConfig()->getVarValue($module, $name);
    }

    protected function _getLayoutStructure()
    {
        if ($this->getLayout()) {
            return $this->getLayout()->getStructure();
        }
        throw new Magento_Exception('Can not get layout structure: there is no layout for block');
    }
}
