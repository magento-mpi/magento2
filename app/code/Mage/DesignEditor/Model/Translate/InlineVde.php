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
     * Translation within the vde will be enabled by the client when the 'Edit' button is enabled.
     *
     * @param mixed $store
     * @return bool
     */
    public function isAllowed($store = null)
    {
        $isAllowed = false;
        if (Mage::getObjectManager()->get('Mage_DesignEditor_Helper_Data')->getTranslationMode() != null) {
            $isAllowed = true;
        }
        return $isAllowed;
    }

    /**
     * Parse and save edited translations.
     *
     * @param array $translateParams
     * @return Mage_DesignEditor_Model_Translate_InlineVde
     */
    public function processAjaxPost($translateParams)
    {
        /** @var $objectManager Magento_ObjectManager */
        $objectManager = Mage::getObjectManager();

        /* @var $resource Mage_Core_Model_Resource_Translate_String */
        $resource = $objectManager->get('Mage_Core_Model_Resource_Translate_String');

        /** @var $validStoreId int */
        $validStoreId = $objectManager->get('Mage_Core_Model_StoreManager')->getStore()->getId();

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
            $urlPrefix = 'adminhtml';
            $urlModel = $objectManager->get('Mage_Backend_Model_Url');
        } else {
            $urlPrefix = 'core';
            $urlModel = $objectManager->get('Mage_Core_Model_Url');
        }
        /** @todo ACB fix bug that required _useVdeFrontend */
        $ajaxUrl = $urlModel->getUrl($urlPrefix . '/ajax/translate',
            array('_secure'=>$store->isCurrentlySecure(),
                  '_useRealRoute' => true,
                  '_useVdeFrontend' => true));
        $trigImg = Mage::getDesign()->getViewFileUrl('Mage_Core::translate_edit_icon.png');
        $trigImgHover = Mage::getDesign()->getViewFileUrl('Mage_Core::translate_edit_icon_hover.png');

        $frameUrl = $objectManager->get('Mage_DesignEditor_Helper_Data')->getCurrentHandleUrl();

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

    <script id="translate-inline-dialog-form-template" type="text/x-jQuery-tmpl">
        <form id="${data.id}">
            {{each(i, item) data.items}}
            <input id="perstore_${i}" name="translate[${i}][perstore]" type="hidden" value="0"/>
            <input name="translate[${i}][original]" type="hidden" value="${item.scope}::${escape(item.original)}"/>
            <input id="custom_${i}" name="translate[${i}][custom]" value="${escape(item.translated)}" data-translate-input="true"/>
            {{/each}}
        </form>
    </script>

    <script id="translate-inline-icon" type="text/x-jQuery-tmpl">
        <img src="${img}" height="16" width="16">
    </script>

    <div id="translate-dialog"></div>

    <script type="text/javascript">
        (function($){
            $(window).load(function() {
                $('body').addClass('trnslate-inline-area');

                $('body').translateInlineDialogVde({
                    ajaxUrl: '<?php echo $ajaxUrl ?>',
                    area: '<?php echo Mage::getDesign()->getArea() ?>',
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
                    imgHover: '<?php echo $trigImgHover ?>',
                    onClick: function(element) {
                        $('body').removeClass('trnslate-inline-area');
                        $('[data-translate]').translateInlineIconVde('hide');
                        $('body').translateInlineDialogVde('open', element);
                    }
                });

                /** todo SDW Remove this once fully implement three inline states */
                parent.jQuery('#vde-translate').translateInlineToggle({
                    frameUrl: '<?php echo $frameUrl ?>',
                    onClick: function(element) {
                        // Display all inline translate options, with the one selected highlighted.
                        parent.jQuery('#vde-translate').translateInlineToggle('toggleTranslateMode', 'text');

                        /** @todo ACB switchClass on 'T' when inline translation is toggled. */
                        //parent.jQuery('#vde-translate').switchClass('', '');
                    }
                });

                parent.jQuery('#vde-translate-text').translateInlineToggle({
                    frameUrl: '<?php echo $frameUrl ?>',
                    onClick: function(element) {
                        parent.jQuery('#vde-translate-text').translateInlineToggle('toggleTranslateMode', 'text');

                        /** @todo ACB switchClass on 'T' when inline text translation is toggled. */
                        //parent.jQuery('#vde-translate-text').switchClass('', '');
                    }
                });

                parent.jQuery('#vde-translate-script').translateInlineToggle({
                    frameUrl: '<?php echo $frameUrl ?>',
                    onClick: function(element) {
                        parent.jQuery('#vde-translate-script').translateInlineToggle('toggleTranslateMode', 'script');

                        /** @todo ACB switchClass on 'T' when inline script translation is toggled. */
                        //parent.jQuery('#vde-translate-script').switchClass('', '');
                    }
                });

                parent.jQuery('#vde-translate-alt').translateInlineToggle({
                    frameUrl: '<?php echo $frameUrl ?>',
                    onClick: function(element) {
                        parent.jQuery('#vde-translate-alt').translateInlineToggle('toggleTranslateMode', 'alt');

                        /** @todo ACB switchClass on 'T' when inline alt translation is toggled. */
                        //parent.jQuery('#vde-translate-alt').switchClass('', '');
                    }
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
