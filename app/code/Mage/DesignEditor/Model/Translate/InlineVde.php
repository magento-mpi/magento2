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
 * Inline translation specific to Vde.
 */
class Mage_DesignEditor_Model_Translate_InlineVde implements Mage_Core_Model_Translate_InlineInterface
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
     * @var Mage_DesignEditor_Helper_Data
     */
    protected $_helper;

    /**
     * @var Mage_Core_Model_Translate_InlineParser
     */
    protected $_parser;

    /**
     * @var Mage_Core_Model_Url
     */
    protected $_url;

    /**
     * Flag about inserted styles and scripts for inline translates
     *
     * @var bool
     */
    protected $_isScriptInserted = false;

    /**
     * Initialize inline translation model specific for vde
     *
     * @param Mage_Core_Model_Translate_InlineParser $parser
     * @param Mage_DesignEditor_Helper_Data $helper
     * @param Mage_Core_Model_Url $url
     */
    public function __construct(
        Mage_Core_Model_Translate_InlineParser $parser,
        Mage_DesignEditor_Helper_Data $helper,
        Mage_Core_Model_Url $url
    ) {
        $this->_parser = $parser;
        $this->_helper = $helper;
        $this->_url = $url;
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
     * @return Mage_DesignEditor_Model_Translate_InlineVde
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
        $ajaxUrl = $this->_url->getUrl('core/ajax/translate',
            array('_secure'=>$store->isCurrentlySecure(),
                  '_useRealRoute' => true,
                  '_useVdeFrontend' => true));

        /** @var $block Mage_Core_Block_Template */
        $block = Mage::getObjectManager()->create('Mage_Core_Block_Template');

        $block->setArea($this->_parser->getDesignPackage()->getArea());
        $block->setAjaxUrl($ajaxUrl);
        $block->setFrameUrl($this->_helper->getCurrentHandleUrl());
        $block->setRefreshCanvas($this->isAllowed());

        $block->setTemplate('Mage_DesignEditor::translate_inline.phtml');
        $block->setTranslateMode($this->_helper->getTranslationMode());

        $html = $block->toHtml();

        $this->_parser->setContent(str_ireplace('</body>', $html . '</body>', $content));

        $this->_isScriptInserted = true;
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
        } else if (self::ELEMENT_IMG == $tagName) {
            $mode = self::MODE_ALT;
        }
        return $mode;
    }
}
