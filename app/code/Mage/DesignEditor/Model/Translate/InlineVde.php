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
class Mage_DesignEditor_Model_Translate_InlineVde extends Mage_Core_Model_Translate_InlineAbstract
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
     * Translation within the vde will be enabled by the client when the 'Edit' button is enabled.
     *
     * @return bool
     */
    public function isAllowed()
    {
        return $this->_objectManager->get('Mage_DesignEditor_Helper_Data')->isAllowed();
    }

    /**
     * Parse and save edited translations.
     *
     * @param array $translateParams
     * @return Mage_DesignEditor_Model_Translate_InlineVde
     */
    public function processAjaxPost($translateParams)
    {
        /* @var $resource Mage_Core_Model_Resource_Translate_String */
        $resource = $this->_objectManager->get('Mage_Core_Model_Resource_Translate_String');

        /** @var $validStoreId int */
        $validStoreId = $this->_objectManager->get('Mage_Core_Model_StoreManager')->getStore()->getId();

        foreach ($translateParams as $param) {
            if (empty($param['perstore'])) {
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
            $this->_content = $body;

            $this->_specialTags();
            $this->_tagAttributes();
            $this->_otherText();
            $this->_insertInlineScriptsHtml();

            $body = $this->_content;
        }

        return $this;
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
        return $tagHtml . '<span class="translate-inline-' . $tagName . '" '
            . $this->_getHtmlAttribute(self::DATA_TRANSLATE, htmlspecialchars('[' . join(',', $trArr) . ']'))
            . ' ' . $this->_getHtmlAttribute(self::TRANSLATE_MODE, $this->_getTranslateMode($tagName))
            . '>' . '</span>';
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
            . ' ' . $this->_getHtmlAttribute(self::DATA_TRANSLATE, htmlspecialchars('[' . join(',', $trArr) . ']'))
            . ' ' . $this->_getHtmlAttribute(self::TRANSLATE_MODE, $this->_getTranslateMode($tagName))
            . substr($tagHtml, strlen($tagName) + 1);
    }

    /**
     * Get inline vde translate mode
     *
     * @param string  $tagName
     * @return string
     */
    protected function _getTranslateMode($tagName)
    {
        $mode = self::MODE_TEXT;
        if (self::ELEMENT_SCRIPT == $tagName) {
            $mode = self::MODE_SCRIPT;
        } else if (self::ELEMENT_IMG == $tagName) {
            $mode = self::MODE_ALT;
        }
        return $mode;
    }

    /**
     * Get span containing data-translate attribute
     *
     * @param string $data
     * @param string $text
     * @return string
     */
    public function _getDataTranslateSpan($data, $text)
    {
        return '<span '. $this->_getHtmlAttribute(self::DATA_TRANSLATE, $data) . ' '
            . $this->_getHtmlAttribute(self::TRANSLATE_MODE, self::MODE_TEXT) . '>' . $text . '</span>';
    }

    /**
     * Add translate js to body
     */
    protected function _insertInlineScriptsHtml()
    {
        if ($this->_isScriptInserted || stripos($this->_content, '</body>') === false) {
            return;
        }

        $store = $this->_objectManager->get('Mage_Core_Model_StoreManager')->getStore();
        $ajaxUrl = $this->_objectManager->get('Mage_Core_Model_Url')->getUrl('core/ajax/translate',
            array('_secure'=>$store->isCurrentlySecure(),
                  '_useRealRoute' => true,
                  '_useVdeFrontend' => true));

        /** @var $block Mage_Core_Block_Template */
        $block = $this->_objectManager->create('Mage_Core_Block_Template');

        $block->setArea($this->_objectManager->get('Mage_Core_Model_Design_Package')->getArea());
        $block->setAjaxUrl($ajaxUrl);
        $block->setFrameUrl($this->_objectManager->get('Mage_DesignEditor_Helper_Data')->getCurrentHandleUrl());
        $block->setRefreshCanvas($this->isAllowed());

        $block->setTemplate('Mage_DesignEditor::translate_inline.phtml');
        $block->setTranslateMode($this->_objectManager->get('Mage_DesignEditor_Helper_Data')->getTranslationMode());

        $html = $block->toHtml();

        $this->_content = str_ireplace('</body>', $html . '</body>', $this->_content);

        $this->_isScriptInserted = true;
    }

    /**
     * Add data-translate-mode attribute
     *
     * @param string $trAttr
     * @return string
     */
    protected function _addTranslateAttribute($trAttr)
    {
        return $trAttr . ' ' . $this->_getHtmlAttribute(self::TRANSLATE_MODE, self::MODE_TEXT) . ' ';
    }
}
