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
 * Inline translate specific to Vde.
 */
class Mage_Core_Model_Translate_InlineVde extends Mage_Core_Model_Translate_InlineAbstract
{
    /**
     * Always default inline translation in vde to disabled.
     * Translation within the vde will be enabled by the client when the 'Edit' button is enabled.
     *
     * @param mixed $store
     * @return bool
     */
    public function isAllowed($store = null)
    {
        /** @todo ACB move to helper and get state from client */
        return false;
    }

    /**
     * Parse and save edited translations.
     *
     * @param array $translate
     * @return Mage_Core_Model_Translate_InlineVde
     */
    public function processAjaxPost($translate)
    {
        /** @todo ACB ensure isAllowed is not needed here. */
        //if (!$this->isAllowed()) {
            //return $this;
        //}

        /* @var $resource Mage_Core_Model_Resource_Translate_String */
        $resource = Mage::getResourceModel('Mage_Core_Model_Resource_Translate_String');
        /** @todo ACB fix variable name */
        foreach ($translate as $t) {
            if (empty($t['perstore'])) {
                $resource->deleteTranslate($t['original'], null, false);
                $storeId = 0;
            } else {
                $storeId = Mage_Core_Model_StoreManager::getStore()->getId();
            }
            $resource->saveTranslate($t['original'], $t['custom'], null, $storeId);
        }
        return $this;
    }

    /**
     * Replace VDE specific translation templates with HTML fragments
     *
     * @param array|string $body
     * @param bool $isJson
     * @return Mage_Core_Model_Translate_InlineVde
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
     * Add translate js to body
     */
    protected function _insertInlineScriptsHtml()
    {
        if ($this->_isScriptInserted || stripos($this->_content, '</body>') === false) {
            return;
        }
        /** @todo ACB shouldn't care about adminhtml.  determine if isAdmin check is needed. */
        if (Mage::app()->getStore()->isAdmin()) {
            $urlPrefix = 'adminhtml';
            $urlModel = Mage::getModel('Mage_Backend_Model_Url');
        } else {
            $urlPrefix = 'core';
            $urlModel = Mage::getModel('Mage_Core_Model_Url');
        }
        $ajaxUrl = $urlModel->getUrl($urlPrefix . '/ajax/translate',
            array('_secure'=>Mage::app()->getStore()->isCurrentlySecure()));
        $trigImg = Mage::getDesign()->getViewFileUrl('Mage_Core::translate_edit_icon.png');

        ob_start();
        $design = Mage::getDesign();
        /** @todo move out to template ACB */
        ?>
    <script type="text/javascript" src="<?php echo $design->getViewFileUrl('prototype/window.js') ?>"></script>
    <link rel="stylesheet" type="text/css" href="<?php echo $design->getViewFileUrl('prototype/windows/themes/default.css') ?>"/>
    <link rel="stylesheet" type="text/css" href="<?php echo $design->getViewFileUrl('Mage_Core::prototype/magento.css') ?>"/>
    <script type="text/javascript" src="<?php echo $design->getViewFileUrl('mage/edit-trigger.js') ?>"></script>
    <script type="text/javascript" src="<?php echo $design->getViewFileUrl('mage/translate-inline-vde.js') ?>"></script>
    <link rel="stylesheet" type="text/css" href="<?php echo $design->getViewFileUrl('mage/translate-inline-vde.css') ?>"/>

    <script type="text/javascript">
        (function($) {
            $(window).load(function() {
                $('body').addClass('trnslate-inline-area');

                $('body').translateInlineDialogVde({
                    onSubmitComplete: function() {
                        $('body').addClass('trnslate-inline-area');
                        $('[data-translate]').translateInlineIconVde('show');
                    },

                    onCancel: function() {
                        $('body').addClass('trnslate-inline-area');
                        $('[data-translate]').translateInlineIconVde('show');
                    }
                });

                $('[data-translate]').translateInlineIconVde({
                    img: '<?php echo $trigImg ?>',
                    ajaxUrl: '<?php echo $ajaxUrl ?>',
                    area: '<?php echo Mage::getDesign()->getArea() ?>',
                    onClick: function(element) {
                        $('body').removeClass('trnslate-inline-area');
                        $('[data-translate]').translateInlineIconVde('hide');
                        $('body').translateInlineDialogVde('open', element);
                    },
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
