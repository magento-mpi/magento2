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
 * Inline Translations PHP part
 */
class Mage_Core_Model_Translate_Inline extends Mage_Core_Model_Translate_InlineAbstract
{
    /**
     * Initialize inline abstract translate model
     *
     * @param Mage_Core_Model_Resource_Translate_String $resource
     * @param Mage_Core_Model_StoreManager $storeManager
     * @param Mage_Core_Model_Url $coreUrl
     * @param Mage_Core_Model_Design_Package $design
     */
    public function __construct(
        Mage_Core_Model_Resource_Translate_String $resource,
        Mage_Core_Model_StoreManager $storeManager,
        Mage_Core_Model_Url $coreUrl,
        Mage_Core_Model_Design_Package $design
    ) {
        parent::__construct($resource, $storeManager, $coreUrl, $design);
    }


    /**
     * Is enabled and allowed Inline Translates
     *
     * @param mixed $store
     * @return bool
     */
    public function isAllowed($store = null)
    {
        if (is_null($store)) {
            $store = Mage::app()->getStore();
        }
        if (!$store instanceof Mage_Core_Model_Store) {
            $store = Mage::app()->getStore($store);
        }

        if (is_null($this->_isAllowed)) {
            if (Mage::getDesign()->getArea() == 'adminhtml') {
                $active = Mage::getStoreConfigFlag('dev/translate_inline/active_admin', $store);
            } else {
                $active = Mage::getStoreConfigFlag('dev/translate_inline/active', $store);
            }
            $this->_isAllowed = $active && Mage::helper('Mage_Core_Helper_Data')->isDevAllowed($store);
        }

        /* @var $translate Mage_Core_Model_Translate */
        $translate = Mage::getSingleton('Mage_Core_Model_Translate');

        return $translate->getTranslateInline() && parent::isAllowed();
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
            . '>' . strtoupper($tagName) . '</span>';
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
        return substr($tagHtml, 0, strlen($tagName) + 1) . ' '
            . $this->_getHtmlAttribute(self::DATA_TRANSLATE, htmlspecialchars('[' . join(',', $trArr) . ']'))
            . substr($tagHtml, strlen($tagName) + 1);
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
        return '<span ' . $this->_getHtmlAttribute(self::DATA_TRANSLATE, $data) . '>' . $text . '</span>';
    }

    /**
     * Add translate js to body
     */
    protected function _insertInlineScriptsHtml()
    {
        if ($this->_isScriptInserted || stripos($this->_content, '</body>') === false) {
            return;
        }

        $store = $this->_storeManager->getStore();
        if ($store->isAdmin()) {
            $urlPrefix = Mage_Backend_Helper_Data::BACKEND_AREA_CODE;
            $urlModel = Mage::getObjectManager()->get('Mage_Backend_Model_Url');
        } else {
            $urlPrefix = 'core';
            $urlModel = $this->_coreUrl;
        }
        $ajaxUrl = $urlModel->getUrl($urlPrefix . '/ajax/translate',
            array('_secure' => $store->isCurrentlySecure()));

        /** @var $block Mage_Core_Block_Template */
        $block = Mage::getObjectManager()->create('Mage_Core_Block_Template');

        $block->setAjaxUrl($ajaxUrl);

        $block->setTemplate('Mage_Core::translate_inline.phtml');

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
        return $trAttr;
    }

}
