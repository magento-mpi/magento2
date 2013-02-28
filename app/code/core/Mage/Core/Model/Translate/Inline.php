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
     * Flag about inserted styles and scripts for inline translates
     *
     * @var bool
     */
    protected $_isScriptInserted    = false;

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
     * Parse and save edited translate
     *
     * @param array $translate
     * @return Mage_Core_Model_Translate_Inline
     */
    public function processAjaxPost($translate)
    {
        if (!$this->isAllowed()) {
            return $this;
        }

        /* @var $resource Mage_Core_Model_Resource_Translate_String */
        $resource = Mage::getResourceModel('Mage_Core_Model_Resource_Translate_String');
        /** @todo fix variable name ACB */
        foreach ($translate as $t) {
            if (Mage::getDesign()->getArea() == 'adminhtml') {
                $storeId = 0;
            } else if (empty($t['perstore'])) {
                $resource->deleteTranslate($t['original'], null, false);
                $storeId = 0;
            } else {
                $storeId = Mage::app()->getStore()->getId();
            }

            $resource->saveTranslate($t['original'], $t['custom'], null, $storeId);
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
            if (Mage::getDesign()->getArea() == 'adminhtml') {
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

        if (Mage::app()->getStore()->isAdmin()) {
            $urlPrefix = 'adminhtml';
            $urlModel = Mage::getModel('Mage_Backend_Model_Url');
        } else {
            $urlPrefix = 'core';
            $urlModel = Mage::getModel('Mage_Core_Model_Url');
        }
        $ajaxUrl = $urlModel->getUrl($urlPrefix . '/ajax/translate',
            array('_secure'=>Mage::app()->getStore()->isCurrentlySecure()));
        $trigImg = Mage::getDesign()->getViewFileUrl('Mage_Core::fam_book_open.png');

        ob_start();
        $design = Mage::getDesign();
        /** @todo move out to template ACB */
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
}
