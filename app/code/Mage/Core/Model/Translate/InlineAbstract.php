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
 * Abstract translate inline class
 */
abstract class Mage_Core_Model_Translate_InlineAbstract implements Mage_Core_Model_Translate_TranslateInterface
{
    /**
     * Default state for jason flag
     */
    const JSON_FLAG_DEFAULT_STATE = false;

    /**
     * Regular Expression for detected and replace translate
     *
     * @var string
     */
    protected $_tokenRegex = '\{\{\{(.*?)\}\}\{\{(.*?)\}\}\{\{(.*?)\}\}\{\{(.*?)\}\}\}';

    /**
     * Response body or JSON content string
     *
     * @var string
     */
    protected $_content;

    /**
     * Flag about inserted styles and scripts for inline translates
     *
     * @var bool
     */
    protected $_isScriptInserted    = false;

    /**
     * Current content is JSON or Response body
     *
     * @var bool
     */
    protected $_isJson              = self::JSON_FLAG_DEFAULT_STATE;

    /**
     * Get max translate block in same tag
     *
     * @var int
     */
    protected $_maxTranslateBlocks    = 7;

    /**
     * Indicator to hold state of whether inline translation is allowed within vde.
     *
     * @var bool
     */
    protected $_isAllowed;

    /**
     * List of global tags
     *
     * @var array
     */
    protected $_allowedTagsGlobal = array(
        'script'    => 'String in Javascript',
        'title'     => 'Page title',
    );

    /**
     * List of simple tags
     *
     * @var array
     */
    protected $_allowedTagsSimple = array(
        'legend'        => 'Caption for the fieldset element',
        'label'         => 'Label for an input element.',
        'button'        => 'Push button',
        'a'             => 'Link label',
        'b'             => 'Bold text',
        'strong'        => 'Strong emphasized text',
        'i'             => 'Italic text',
        'em'            => 'Emphasized text',
        'u'             => 'Underlined text',
        'sup'           => 'Superscript text',
        'sub'           => 'Subscript text',
        'span'          => 'Span element',
        'small'         => 'Smaller text',
        'big'           => 'Bigger text',
        'address'       => 'Contact information',
        'blockquote'    => 'Long quotation',
        'q'             => 'Short quotation',
        'cite'          => 'Citation',
        'caption'       => 'Table caption',
        'abbr'          => 'Abbreviated phrase',
        'acronym'       => 'An acronym',
        'var'           => 'Variable part of a text',
        'dfn'           => 'Term',
        'strike'        => 'Strikethrough text',
        'del'           => 'Deleted text',
        'ins'           => 'Inserted text',
        'h1'            => 'Heading level 1',
        'h2'            => 'Heading level 2',
        'h3'            => 'Heading level 3',
        'h4'            => 'Heading level 4',
        'h5'            => 'Heading level 5',
        'h6'            => 'Heading level 6',
        'center'        => 'Centered text',
        'select'        => 'List options',
        'img'           => 'Image',
        'input'         => 'Form element',
    );

    /**
     * Strip inline translations from text
     *
     * @param array|string $body
     * @return Mage_Core_Model_Translate_InlineAbstract
     */
    public function stripInlineTranslations(&$body)
    {
        if (is_array($body)) {
            foreach ($body as &$part) {
                $this->stripInlineTranslations($part);
            }
        } else if (is_string($body)) {
            $body = preg_replace('#' . $this->_tokenRegex . '#', '$1', $body);
        }
        return $this;
    }

    /**
     * Parse and save edited translate
     *
     * @param array $translateParams
     * @return Mage_Core_Model_Translate_Inline
     */
    public function processAjaxPost($translateParams)
    {
        if (!$this->isAllowed()) {
            return $this;
        }

        /** @var $objectManager Magento_ObjectManager */
        $objectManager = Mage::getObjectManager();

        /* @var $resource Mage_Core_Model_Resource_Translate_String */
        $resource = $objectManager->get('Mage_Core_Model_Resource_Translate_String');

        /** @var $validStoreId int */
        $validStoreId = $objectManager->get('Mage_Core_Model_StoreManager')->getStore()->getId();

        foreach ($translateParams as $param) {
            if (Mage::getDesign()->getArea() == Mage_Backend_Helper_Data::BACKEND_AREA_CODE) {
                $storeId = 0;
            } else if (empty($param['perstore'])) {
                $resource->deleteTranslate($param['original'], null, false);
                $storeId = 0;
            } else {
                $storeId = $validStoreId;
            }

            $resource->saveTranslate($param['original'], $param['custom'], null, $storeId);
        }

        return $this;
    }

    /**
     * Replace translation templates with HTML fragments
     *
     * @param array|string $body
     * @param bool $isJson
     * @return Mage_Core_Model_Translate_Inline
     */
    public function processResponseBody(&$body, $isJson)
    {
        $this->_setIsJson($isJson);
        if (!$this->isAllowed()) {
            if (Mage::getDesign()->getArea() == Mage_Backend_Helper_Data::BACKEND_AREA_CODE) {
                $this->stripInlineTranslations($body);
            }
            return $this;
        }

        if (is_array($body)) {
            foreach ($body as &$part) {
                $this->processResponseBody($part, $isJson);
            }
        } elseif (is_string($body)) {
            $this->_content = $body;

            $this->_specialTags();
            $this->_tagAttributes();
            $this->_otherText();
            $this->_insertInlineScriptsHtml();

            $body = $this->_content;
        }
        $this->_setIsJson(self::JSON_FLAG_DEFAULT_STATE);
        return $this;
    }

    /**
     * Add translate js to body
     */
    protected function _insertInlineScriptsHtml()
    {
        if ($this->_isScriptInserted || stripos($this->_content, '</body>') === false) {
            return;
        }

        /** @var $objectManager Magento_ObjectManager */
        $objectManager = Mage::getObjectManager();

        $store = $objectManager->get('Mage_Core_Model_StoreManager')->getStore();
        if ($store->isAdmin()) {
            $urlPrefix = Mage_Backend_Helper_Data::BACKEND_AREA_CODE;
            $urlModel = $objectManager->get('Mage_Backend_Model_Url');
        } else {
            $urlPrefix = 'core';
            $urlModel = $objectManager->get('Mage_Core_Model_Url');
        }
        $ajaxUrl = $urlModel->getUrl($urlPrefix . '/ajax/translate',
            array('_secure' => $store->isCurrentlySecure()));
        $trigImg = Mage::getDesign()->getViewFileUrl('Mage_Core::fam_book_open.png');

        ob_start();
        $design = Mage::getDesign();
        ?>
    <script type="text/javascript" src="<?php echo $design->getViewFileUrl('prototype/window.js') ?>"></script>
    <link rel="stylesheet" type="text/css" href="<?php echo $design->getViewFileUrl('prototype/windows/themes/default.css') ?>"/>
    <link rel="stylesheet" type="text/css" href="<?php echo $design->getViewFileUrl('Mage_Core::prototype/magento.css') ?>"/>
    <script type="text/javascript" src="<?php echo $design->getViewFileUrl('mage/edit-trigger.js') ?>"></script>
    <script type="text/javascript" src="<?php echo $design->getViewFileUrl('mage/translate-inline.js') ?>"></script>
    <link rel="stylesheet" type="text/css" href="<?php echo $design->getViewFileUrl('mage/translate-inline.css') ?>"/>

    <script type="text/javascript">
        (function($){
            $(document).ready(function() {
                $(this).translateInline({
                    ajaxUrl: '<?php echo $ajaxUrl ?>',
                    area: '<?php echo Mage::getDesign()->getArea() ?>',
                    editTrigger: {img: '<?php echo $trigImg ?>'}
                });
            });
        })(jQuery);
    </script>
    <?php
        $html = ob_get_clean();

        $this->_content = str_ireplace('</body>', $html . '</body>', $this->_content);

        $this->_isScriptInserted = true;
    }

    /**
     * Escape Translate data
     *
     * @param string $string
     * @return string
     */
    protected function _escape($string)
    {
        return str_replace("'", "\\'", htmlspecialchars($string));
    }

    /**
     * Get attribute location
     *
     * @param array $matches
     * @param array $options
     * @return string
     */
    protected function _getAttributeLocation($matches, $options)
    {
        return 'Tag attribute (ALT, TITLE, etc.)';
    }

    /**
     * Get tag location
     *
     * @param array $matches
     * @param array $options
     * @return string
     */
    protected function _getTagLocation($matches, $options)
    {
        $tagName = strtolower($options['tagName']);

        if (isset($options['tagList'][$tagName])) {
            return $options['tagList'][$tagName];
        }

        return ucfirst($tagName) . ' Text';
    }

    /**
     * Get translate data by regexp
     *
     * @param string $regexp
     * @param string $text
     * @param string|array $locationCallback
     * @param array $options
     * @return array
     */
    protected function _getTranslateData($regexp, &$text, $locationCallback, $options = array())
    {
        $trArr = array();
        $next = 0;
        while (preg_match($regexp, $text, $matches, PREG_OFFSET_CAPTURE, $next)) {
            $trArr[] = json_encode(array(
                'shown' => $matches[1][0],
                'translated' => $matches[2][0],
                'original' => $matches[3][0],
                'location' => call_user_func($locationCallback, $matches, $options),
                'scope' => $matches[4][0],
            ));
            $text = substr_replace($text, $matches[1][0], $matches[0][1], strlen($matches[0][0]));
            $next = $matches[0][1];
        }
        return $trArr;
    }


    /**
     * Prepare tags inline translates
     *
     */
    protected function _tagAttributes()
    {
        $this->_prepareTagAttributesForContent($this->_content);
    }

    /**
     * Prepare tags inline translates for the content
     *
     * @param string $content
     */
    protected function _prepareTagAttributesForContent(&$content)
    {
        if ($this->_isJson) {
            $quoteHtml   = '\"';
        } else {
            $quoteHtml   = '"';
        }

        $tagMatch   = array();
        $nextTag    = 0;
        $tagRegExp = '#<([a-z]+)\s*?[^>]+?((' . $this->_tokenRegex . ')[^>]*?)+\\\\?/?>#iS';
        while (preg_match($tagRegExp, $content, $tagMatch, PREG_OFFSET_CAPTURE, $nextTag)) {
            $tagHtml = $tagMatch[0][0];
            $matches = array();
            $attrRegExp = '#' . $this->_tokenRegex . '#S';
            $trArr = $this->_getTranslateData($attrRegExp, $tagHtml, array($this, '_getAttributeLocation'));
            if ($trArr) {
                $transRegExp = '# data-translate=' . $quoteHtml . '\[([^' . preg_quote($quoteHtml) . ']*)]'
                    . $quoteHtml . '#i';
                if (preg_match($transRegExp, $tagHtml, $matches)) {
                    $tagHtml = str_replace($matches[0], '', $tagHtml); //remove tra
                    $trAttr  = ' data-translate=' . $quoteHtml
                        . htmlspecialchars('[' . $matches[1] . ',' . join(',', $trArr) . ']') . $quoteHtml;
                } else {
                    $trAttr  = ' data-translate=' . $quoteHtml
                        . htmlspecialchars('[' . join(',', $trArr) . ']') . $quoteHtml;
                }
                $tagHtml = substr_replace($tagHtml, $trAttr, strlen($tagMatch[1][0]) + 1, 1);
                $content = substr_replace($content, $tagHtml, $tagMatch[0][1], strlen($tagMatch[0][0]));
            }
            $nextTag = $tagMatch[0][1] + strlen($tagHtml);
        }
    }

    /**
     * Get html quote symbol
     *
     * @return string
     */
    protected function _getHtmlQuote()
    {
        if ($this->_isJson) {
            return '\"';
        } else {
            return '"';
        }
    }

    /**
     * Prepare special tags
     */
    protected function _specialTags()
    {
        $this->_translateTags($this->_content, $this->_allowedTagsGlobal, '_applySpecialTagsFormat', false);
        $this->_translateTags($this->_content, $this->_allowedTagsSimple, '_applySimpleTagsFormat', true);
    }

    /**
     * Format translate for special tags
     *
     * @param string $tagHtml
     * @param string $tagName
     * @param array $trArr
     * @return string
     */
    protected function _applySpecialTagsFormat($tagHtml, $tagName, $trArr)
    {
        return $tagHtml . '<span class="translate-inline-' . $tagName
            . '" data-translate='
            . $this->_getHtmlQuote()
            . htmlspecialchars('[' . join(',', $trArr) . ']')
            . $this->_getHtmlQuote() . '>'
            . strtoupper($tagName) . '</span>';
    }

    /**
     * Format translate for simple tags
     *
     * @param string $tagHtml
     * @param string  $tagName
     * @param array $trArr
     * @return string
     */
    protected function _applySimpleTagsFormat($tagHtml, $tagName, $trArr)
    {
        return substr($tagHtml, 0, strlen($tagName) + 1)
            . ' data-translate='
            . $this->_getHtmlQuote() . htmlspecialchars('[' . join(',', $trArr) . ']')
            . $this->_getHtmlQuote()
            . substr($tagHtml, strlen($tagName) + 1);
    }

    /**
     * Prepare simple tags
     *
     * @param string $content
     * @param array $tagsList
     * @param string|array $formatCallback
     * @param bool $isNeedTranslateAttributes
     */
    protected function _translateTags(&$content, $tagsList, $formatCallback, $isNeedTranslateAttributes)
    {
        $nextTag = 0;

        $tags = implode('|', array_keys($tagsList));
        $tagRegExp  = '#<(' . $tags . ')(/?>| \s*[^>]*+/?>)#iSU';
        $tagMatch = array();
        while (preg_match($tagRegExp, $content, $tagMatch, PREG_OFFSET_CAPTURE, $nextTag)) {
            $tagName  = strtolower($tagMatch[1][0]);
            if (substr($tagMatch[0][0], -2) == '/>') {
                $tagClosurePos = $tagMatch[0][1] + strlen($tagMatch[0][0]);
            } else {
                $tagClosurePos = $this->findEndOfTag($content, $tagName, $tagMatch[0][1]);
            }

            if ($tagClosurePos === false) {
                $nextTag += strlen($tagMatch[0][0]);
                continue;
            }

            $tagLength = $tagClosurePos - $tagMatch[0][1];

            $tagStartLength = strlen($tagMatch[0][0]);

            $tagHtml = $tagMatch[0][0]
                . substr($content, $tagMatch[0][1] + $tagStartLength, $tagLength - $tagStartLength);
            $tagClosurePos = $tagMatch[0][1] + strlen($tagHtml);

            $trArr = $this->_getTranslateData(
                '#' . $this->_tokenRegex . '#iS',
                $tagHtml,
                array($this, '_getTagLocation'),
                array(
                    'tagName' => $tagName,
                    'tagList' => $tagsList
                )
            );

            if (!empty($trArr)) {
                $trArr = array_unique($trArr);
                $tagHtml = call_user_func(array($this, $formatCallback), $tagHtml, $tagName, $trArr);
                $tagClosurePos = $tagMatch[0][1] + strlen($tagHtml);
                $content = substr_replace($content, $tagHtml, $tagMatch[0][1], $tagLength);
            }
            $nextTag = $tagClosurePos;
        }
    }

    /**
     * Find end of tag
     *
     * @param string $body
     * @param string $tagName
     * @param int $from
     * @return bool|int return false if end of tag is not found
     */
    private function findEndOfTag($body, $tagName, $from)
    {
        $openTag = '<' . $tagName;
        $closeTag =  ($this->_isJson ? '<\\/' : '</') . $tagName;
        $tagLength = strlen($tagName);
        $length = $tagLength + 1;
        $end = $from + 1;
        while (substr_count($body, $openTag, $from, $length) !== substr_count($body, $closeTag, $from, $length)) {
            $end = strpos($body, $closeTag, $end + $tagLength + 1);
            if ($end === false) {
                return false;
            }
            $length = $end - $from  + $tagLength + 3;
        }
        if (preg_match('#<\\\\?\/' . $tagName .'\s*?>#i', $body, $tagMatch, null, $end)) {
            return $end + strlen($tagMatch[0]);
        } else {
            return false;
        }
    }

    /**
     * Prepare other text inline translates
     */
    protected function _otherText()
    {
        if ($this->_isJson) {
            $quoteHtml = '\"';
        } else {
            $quoteHtml = '"';
        }

        $next = 0;
        $matches = array();
        while (preg_match('#' . $this->_tokenRegex . '#', $this->_content, $matches, PREG_OFFSET_CAPTURE, $next)) {
            $tr = json_encode(array(
                'shown' => $matches[1][0],
                'translated' => $matches[2][0],
                'original' => $matches[3][0],
                'location' => 'Text',
                'scope' => $matches[4][0],
            ));

            $spanHtml = '<span data-translate=' . $quoteHtml . htmlspecialchars('[' . $tr . ']') . $quoteHtml
                . '>' . $matches[1][0] . '</span>';
            $this->_content = substr_replace($this->_content, $spanHtml, $matches[0][1], strlen($matches[0][0]));
            $next = $matches[0][1] + strlen($spanHtml) - 1;
        }
    }

    /**
     * Set flag about parsed content is Json
     *
     * @param bool $flag
     * @return Mage_Core_Model_Translate_InlineAbstract
     */
    protected function _setIsJson($flag)
    {
        $this->_isJson = $flag;
        return $this;
    }

    /**
     * Is enabled and allowed Inline Translates
     *
     * @return bool
     */
    public function isAllowed()
    {
        return $this->_isAllowed;
    }
}
