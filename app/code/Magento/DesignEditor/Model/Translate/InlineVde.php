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
 * Inline translation specific to Vde.
 */
class Magento_DesignEditor_Model_Translate_InlineVde implements Magento_Core_Model_Translate_InlineInterface
{
    /**
     * data-translate-mode attribute name
     */
    const TRANSLATE_MODE = 'data-translate-mode';

    /**
     * text translate mode
     */
    const MODE_TEXT = 'text';

    /**
     * img element name
     */
    const ELEMENT_IMG = 'img';

    /**
     * alt translate mode
     */
    const MODE_ALT = 'alt';

    /**
     * script translate mode
     */
    const MODE_SCRIPT = 'script';

    /**
     * script element name
     */
    const ELEMENT_SCRIPT = self::MODE_SCRIPT;

    /**
     * @var Magento_DesignEditor_Helper_Data
     */
    protected $_helper;

    /**
     * @var Magento_Core_Model_Translate_InlineParser
     */
    protected $_parser;

    /**
     * @var Magento_Core_Model_Url
     */
    protected $_url;

    /**
     * Flag about inserted styles and scripts for inline translates
     *
     * @var bool
     */
    protected $_isScriptInserted = false;

    /**
     * @var Magento_ObjectManager
     */
    protected $_objectManager;

    /**
     * Initialize inline translation model specific for vde
     *
     * @param Magento_Core_Model_Translate_InlineParser $parser
     * @param Magento_DesignEditor_Helper_Data $helper
     * @param Magento_Core_Model_Url $url
     * @param Magento_ObjectManager $objectManager
     */
    public function __construct(
        Magento_Core_Model_Translate_InlineParser $parser,
        Magento_DesignEditor_Helper_Data $helper,
        Magento_Core_Model_Url $url,
        Magento_ObjectManager $objectManager
    ) {
        $this->_parser = $parser;
        $this->_helper = $helper;
        $this->_url = $url;
        $this->_objectManager = $objectManager;
    }

    /**
     * Translation within the vde will be enabled by the client when the 'Edit' button is enabled.
     *
     * @return bool
     */
    public function isAllowed()
    {
        return $this->_helper->isAllowed();
    }

    /**
     * Replace VDE specific translation templates with HTML fragments
     *
     * @param array|string $body
     * @param bool $isJson
     * @return Magento_DesignEditor_Model_Translate_InlineVde
     */
    public function processResponseBody(&$body, $isJson)
    {
        if (is_array($body)) {
            foreach ($body as &$part) {
                $this->processResponseBody($part, $isJson);
            }
        } elseif (is_string($body)) {
            $content = $this->_parser->processResponseBodyString($body, $this);
            $this->_insertInlineScriptsHtml($content);
            $body = $this->_parser->getContent();
        }
        return $this;
    }

    /**
     * Returns the translation mode html attribute needed by vde to specify which translation mode the
     * element represents.
     *
     * @param mixed|string $tagName
     * @return string
     */
    public function getAdditionalHtmlAttribute($tagName = null)
    {
        return self::TRANSLATE_MODE . '="' . $this->_getTranslateMode($tagName) . '"';
    }

    /**
     * Create block to render script and html with added inline translation content specific for vde.
     */
    private function _insertInlineScriptsHtml($content)
    {
        if ($this->_isScriptInserted || stripos($content, '</body>') === false) {
            return;
        }

        $store = $this->_parser->getStoreManager()->getStore();
        $ajaxUrl = $this->_url->getUrl('core/ajax/translate', array(
            '_secure' => $store->isCurrentlySecure(),
            Magento_DesignEditor_Helper_Data::TRANSLATION_MODE => $this->_helper->getTranslationMode()
        ));

        /** @var $block Magento_Core_Block_Template */
        $block = $this->_objectManager->create('Magento_Core_Block_Template');

        $block->setArea($this->_parser->getDesignPackage()->getArea());
        $block->setAjaxUrl($ajaxUrl);

        $block->setFrameUrl($this->_getFrameUrl());
        $block->setRefreshCanvas($this->isAllowed());

        $block->setTemplate('Magento_DesignEditor::translate_inline.phtml');
        $block->setTranslateMode($this->_helper->getTranslationMode());

        $this->_parser->setContent(str_ireplace('</body>', $block->toHtml() . '</body>', $content));

        $this->_isScriptInserted = true;
    }

    /**
     * Generate frame url
     *
     * @return string
     */
    protected function _getFrameUrl()
    {
        /** @var Magento_Backend_Model_Session $backendSession */
        $backendSession = $this->_objectManager->get('Magento_Backend_Model_Session');

        /** @var $vdeUrlModel Magento_DesignEditor_Model_Url_NavigationMode */
        $vdeUrlModel = $this->_objectManager->create('Magento_DesignEditor_Model_Url_NavigationMode');
        $currentUrl = $backendSession->getData(Magento_DesignEditor_Model_State::CURRENT_URL_SESSION_KEY);

        return $vdeUrlModel->getUrl(ltrim($currentUrl, '/'));
    }

    /**
     * Get inline vde translate mode
     *
     * @param string  $tagName
     * @return string
     */
    private function _getTranslateMode($tagName)
    {
        $mode = self::MODE_TEXT;
        if (self::ELEMENT_SCRIPT == $tagName) {
            $mode = self::MODE_SCRIPT;
        } elseif (self::ELEMENT_IMG == $tagName) {
            $mode = self::MODE_ALT;
        }
        return $mode;
    }
}
