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
     * Format translation for special tags.  Adding translate mode attribute for vde requests.
     *
     * @param string $tagHtml
     * @param string $tagName
     * @param array $trArr
     * @return string
     */
    public function applySpecialTagsFormat($tagHtml, $tagName, $trArr)
    {
        return $tagHtml . '<span class="translate-inline-' . $tagName . '" '
            . $this->_parser->getHtmlAttribute(Mage_Core_Model_Translate_InlineParser::DATA_TRANSLATE,
                htmlspecialchars('['
            . join(',', $trArr) . ']')) . ' '
            . $this->_parser->getHtmlAttribute(self::TRANSLATE_MODE, $this->_getTranslateMode($tagName))
            . '>' . '</span>';
    }

    /**
     * Format translation for simple tags.  Added translate mode attribute for vde requests.
     *
     * @param string $tagHtml
     * @param string  $tagName
     * @param array $trArr
     * @return string
     */
    public function applySimpleTagsFormat($tagHtml, $tagName, $trArr)
    {
        return substr($tagHtml, 0, strlen($tagName) + 1) . ' '
            . $this->_parser->getHtmlAttribute(Mage_Core_Model_Translate_InlineParser::DATA_TRANSLATE,
                htmlspecialchars('['
            . join(',', $trArr) . ']')) . ' '
            . $this->_parser->getHtmlAttribute(self::TRANSLATE_MODE, $this->_getTranslateMode($tagName))
            . substr($tagHtml, strlen($tagName) + 1);
    }

    /**
     * Add data-translate-mode attribute
     *
     * @param string $trAttr
     * @return string
     */
    public function addTranslateAttribute($trAttr)
    {
        return $trAttr . ' ' . $this->_parser->getHtmlAttribute(self::TRANSLATE_MODE, self::MODE_TEXT) . ' ';
    }

    /**
     * Returns the html span that contains the data translate attribute including vde specific translate mode attribute
     *
     * @param string $data
     * @param string $text
     * @return string
     */
    public function getDataTranslateSpan($data, $text)
    {
        return '<span '. $this->_parser->getHtmlAttribute(Mage_Core_Model_Translate_InlineParser::DATA_TRANSLATE,
                $data) . ' '
            . $this->_parser->getHtmlAttribute(self::TRANSLATE_MODE, self::MODE_TEXT) . '>' . $text . '</span>';
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

        //$this->_content = str_ireplace('</body>', $html . '</body>', $this->_content);
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
