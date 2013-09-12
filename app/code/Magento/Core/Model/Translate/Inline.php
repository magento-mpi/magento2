<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Core
 * @copyright   {copyright}
 * @license     {license_link}
 */


/**
 * Inline Translations PHP part
 */
class Magento_Core_Model_Translate_Inline implements Magento_Core_Model_Translate_InlineInterface
{
    /**
     * Regular Expression for detected and replace translate
     *
     * @var string
     */
    protected $_tokenRegex = '\{\{\{(.*?)\}\}\{\{(.*?)\}\}\{\{(.*?)\}\}\{\{(.*?)\}\}\}';

    /**
     * @var Magento_Core_Model_Translate
     */
    protected $_translator;
    /**
     * Indicator to hold state of whether inline translation is allowed
     *
     * @var bool
     */
    protected $_isAllowed;

    /**
     * @var Magento_Core_Model_Translate_InlineParser
     */
    protected $_parser;

    /**
     * Flag about inserted styles and scripts for inline translates
     *
     * @var bool
     */
    protected $_isScriptInserted    = false;

    /**
     * Core store config
     *
     * @var Magento_Core_Model_Store_Config
     */
    protected $_coreStoreConfig;

    /**
     * Initialize inline translation model
     *
     * @param Magento_Core_Model_Translate_InlineParser $parser
     * @param Magento_Core_Model_Translate $translate
     * @param Magento_Core_Model_Store_Config $coreStoreConfig
     */
    public function __construct(
        Magento_Core_Model_Translate_InlineParser $parser,
        Magento_Core_Model_Translate $translate,
        Magento_Core_Model_Store_Config $coreStoreConfig
    ) {
        $this->_coreStoreConfig = $coreStoreConfig;
        $this->_parser = $parser;
        $this->_translator = $translate;
    }

    /**
     * Is enabled and allowed Inline Translates
     *
     * @param mixed $store
     * @return bool
     */
    public function isAllowed($store = null)
    {
        if (is_null($this->_isAllowed)) {
            if (is_null($store)) {
                $store = $this->_parser->getStoreManager()->getStore();
            }
            if (!$store instanceof Magento_Core_Model_Store) {
                $store = $this->_parser->getStoreManager()->getStore($store);
            }

            if ($this->_parser->getDesignPackage()->getArea() == 'adminhtml') {
                $active = $this->_coreStoreConfig->getConfigFlag('dev/translate_inline/active_admin', $store);
            } else {
                $active = $this->_coreStoreConfig->getConfigFlag('dev/translate_inline/active', $store);
            }
            $this->_isAllowed = $active && $this->_parser->getHelper()->isDevAllowed($store);
        }
        return $this->_translator->getTranslateInline() && $this->_isAllowed;
    }

    /**
     * Replace translation templates with HTML fragments
     *
     * @param array|string $body
     * @param bool $isJson
     * @return Magento_Core_Model_Translate_Inline
     */
    public function processResponseBody(&$body, $isJson)
    {
        $this->_parser->setIsJson($isJson);
        if (!$this->isAllowed()) {
            if ($this->_parser->getDesignPackage()->getArea() == Magento_Backend_Helper_Data::BACKEND_AREA_CODE) {
                $this->_stripInlineTranslations($body);
            }
            return $this;
        }

        if (is_array($body)) {
            foreach ($body as &$part) {
                $this->processResponseBody($part, $isJson);
            }
        } elseif (is_string($body)) {
            $content = $this->_parser->processResponseBodyString($body, $this);
            $this->_insertInlineScriptsHtml($content);
            $body = $this->_parser->getContent();
        }
        $this->_parser->setIsJson(Magento_Core_Model_Translate_InlineParser::JSON_FLAG_DEFAULT_STATE);
        return $this;
    }

    /**
     * Additional translation mode html attribute is not needed for base inline translation.
     *
     * @param mixed|string $tagName
     * @return string
     */
    public function getAdditionalHtmlAttribute($tagName = null)
    {
        return null;
    }

    /**
     * Create block to render script and html with added inline translation content.
     */
    private function _insertInlineScriptsHtml($content)
    {
        if ($this->_isScriptInserted || stripos($content, '</body>') === false) {
            return;
        }

        $store = $this->_parser->getStoreManager()->getStore();
        if ($store->isAdmin()) {
            $urlPrefix = Magento_Backend_Helper_Data::BACKEND_AREA_CODE;
            $urlModel = Mage::getObjectManager()->get('Magento_Backend_Model_Url');
        } else {
            $urlPrefix = 'core';
            $urlModel = Mage::getObjectManager()->get('Magento_Core_Model_Url');
        }
        $ajaxUrl = $urlModel->getUrl($urlPrefix . '/ajax/translate',
            array('_secure' => $store->isCurrentlySecure()));

        /** @var $block Magento_Core_Block_Template */
        $block = Mage::getObjectManager()->create('Magento_Core_Block_Template');

        $block->setAjaxUrl($ajaxUrl);

        $block->setTemplate('Magento_Core::translate_inline.phtml');

        $html = $block->toHtml();

        $this->_parser->setContent(str_ireplace('</body>', $html . '</body>', $content));

        $this->_isScriptInserted = true;
    }

    /**
     * Strip inline translations from text
     *
     * @param array|string $body
     * @return Magento_Core_Model_Translate_Inline
     */
    private function _stripInlineTranslations(&$body)
    {
        if (is_array($body)) {
            foreach ($body as &$part) {
                $this->_stripInlineTranslations($part);
            }
        } else if (is_string($body)) {
            $body = preg_replace('#' . $this->_tokenRegex . '#', '$1', $body);
        }
        return $this;
    }
}
