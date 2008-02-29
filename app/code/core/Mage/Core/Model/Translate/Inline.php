<?php
/**
 * Magento
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/osl-3.0.php
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@magentocommerce.com so we can send you a copy immediately.
 *
 * @category   Mage
 * @package    Mage_Core
 * @copyright  Copyright (c) 2004-2007 Irubin Consulting Inc. DBA Varien (http://www.varien.com)
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */


/**
 * Inline Translations PHP part
 *
 * @author  Moshe Gurvich <moshe@varien.com>
 */
class Mage_Core_Model_Translate_Inline
{
    protected $_tokenRegex = '<<<(.*?)>><<(.*?)>><<(.*?)>><<(.*?)>>>';
    protected $_content;

    public function processResponseBody(&$bodyArray)
    {
        if (!Mage::getStoreConfigFlag('dev/locale/translate_inline')) {
            return;
        }

        $trRegex = '';

        foreach ($bodyArray as $i=>$content) {
            $this->_content = $content;

            $this->_tagAttributes();
            $this->_specialTags();
            #$this->_tagsInnerHtml();
            $this->_otherText();

            $bodyArray[$i] = $this->_content;
        }

        $baseJsUrl = Mage::getBaseUrl('js');
        $bodyArray[] = '
            <script type="text/javascript" src="'.$baseJsUrl.'prototype/effects.js"></script>
            <script type="text/javascript" src="'.$baseJsUrl.'prototype/window.js"></script>
            <link rel="stylesheet" type="text/css" href="'.$baseJsUrl.'prototype/windows/themes/default.css"/>
            <link rel="stylesheet" type="text/css" href="'.$baseJsUrl.'prototype/windows/themes/alphacube.css"/>
        ';

        $ajaxUrl = Mage::getUrl('core/ajax/translate');
        $trigImg = Mage::getDesign()->getSkinUrl('images/fam_book_open.png');

        $bodyArray[] = <<<EOT
<div id="translate-inline-trig"><img src="{$trigImg}"/></div>
<script type="text/javascript">

function escapeHTML(str)
{
   var div = document.createElement('div');
   var text = document.createTextNode(str);
   div.appendChild(text);
   var escaped = div.innerHTML;
   escaped = escaped.replace(/"/g, '&quot;');
   return escaped;
};

var translateInlineTrig = $('translate-inline-trig');
var translateInlineTrigTimer;
var translateInlineTrigEl;

translateInlineTrig.observe('click', translateInlineShowForm.bindAsEventListener());
translateInlineTrig.observe('mouseover', function(event) { clearInterval(translateInlineTrigTimer) } );
translateInlineTrig.observe('mouseout', translateInlineTrigHide.bindAsEventListener());

function translateInlineTrigShow(event) {
    var el = Event.element(event);
    var p = el.up('.translate-inline');
    if (p) {
        var tag = p.tagName.toLowerCase();
        if (tag=='button') {
            el = p;
        }
    }

    clearInterval(translateInlineTrigTimer);

    var p = Position.cumulativeOffset(el);

    translateInlineTrig.style.left = p[0]+'px';
    translateInlineTrig.style.top = p[1]+'px';
    translateInlineTrig.style.display = 'block';

    translateInlineTrigEl = el;
}

function translateInlineTrigHide(event, el) {
    translateInlineTrigTimer = window.setTimeout(function() {
        translateInlineTrig.style.display = 'none';
        translateInlineTriggerEl = null;
    }, 200);
}

function translateInlineShowForm(event) {
    var el = translateInlineTrigEl;
    if (!el) {
        return;
    }

    eval('var data = '+el.getAttribute('translate'));

    var content = '<form id="translate-inline-form"><table cellspacing="0">';
    var t = new Template(
        '<tr><td class="label">Scope: </td><td class="value">#{scope}</td></tr>'+
        '<tr><td class="label">Shown: </td><td class="value">#{shown_escape}</td></tr>'+
        '<tr><td class="label">Original: </td><td class="value">#{original_escape}</td></tr>'+
        '<tr><td class="label">Translated: </td><td class="value">#{translated_escape}</td></tr>'+
        '<tr><td class="label">Custom: </td><td class="value">'+
            '<input name="translate[#{i}][original]" type="hidden" value="#{scope}::#{original_escape}"/>'+
            '<input name="translate[#{i}][custom]" class="input-text" value="#{translated_escape}"/>'+
        '</td></tr>'+
        '<tr><td colspan="2"><hr/></td></tr>'
    );
    for (i=0; i<data.length; i++) {
        data[i]['i'] = i;
        data[i]['shown_escape'] = escapeHTML(data[i]['shown']);
        data[i]['translated_escape'] = escapeHTML(data[i]['translated']);
        data[i]['original_escape'] = escapeHTML(data[i]['original']);
        content += t.evaluate(data[i]);
    }
    content += '</table></form>';

    Dialog.confirm(content, {
        draggable:true,
        resizable:true,
        closable:true,
        className:"alphacube",
        title:"Translation",
        width:500,
        height:400,
        //recenterAuto:false,
        hideEffect:Element.hide,
        showEffect:Element.show,
        id:"translate-inline",
        buttonClass:"form-button",
        okLabel:"Submit",
        ok: function(win) {
            var inputs = $('translate-inline-form').getInputs(), parameters = [];
            for (var i=0; i<inputs.length; i++) {
                parameters[inputs[i].name] = inputs[i].value;
            }
            new Ajax.Request('{$ajaxUrl}', {
                method:'post',
                parameters:parameters
            });
            win.close();
        }
    });
}
$$('*[translate]').each(function(el){
    el.addClassName('translate-inline');
    Event.observe(el, 'mouseover', translateInlineTrigShow.bindAsEventListener(el));
    Event.observe(el, 'mouseout', translateInlineTrigHide.bindAsEventListener(el));
});

</script>
</body>
EOT;
    }


    protected function _tagAttributes()
    {
        $nextTag = 0; $i=0;
        while (preg_match('#<([a-z]+)\s*?[^>]+?(('.$this->_tokenRegex.')[^/>]*?)+/?(>)#i',
            $this->_content, $tagMatch, PREG_OFFSET_CAPTURE, $nextTag)) {

            $next = 0;
            $tagHtml = $tagMatch[0][0];
            $trArr = array();

            while (preg_match('#'.$this->_tokenRegex.'#i',
                $tagHtml, $m, PREG_OFFSET_CAPTURE, $next)) {

                $trArr[] = '{shown:\''.htmlspecialchars($m[1][0]).'\','
                    .'translated:\''.htmlspecialchars($m[2][0]).'\','
                    .'original:\''.htmlspecialchars($m[3][0]).'\','
                    .'scope:\''.htmlspecialchars($m[4][0]).'\'}';
                $tagHtml = substr_replace($tagHtml, $m[1][0], $m[0][1], strlen($m[0][0]));
                $next = $m[0][1];
            }

            $trAttr = ' translate="['.join(',', $trArr).']"';
            $tagHtml = preg_replace('#/?>$#', $trAttr.'$0', $tagHtml);

            $this->_content = substr_replace($this->_content, $tagHtml, $tagMatch[0][1], $tagMatch[8][1]+1-$tagMatch[0][1]);
            $nextTag = $tagMatch[0][1];
        }
    }

    protected function _specialTags()
    {
        $nextTag = 0;

        while (preg_match('#<(script|title|select|button)[^>]+(>)#i',
            $this->_content, $tagMatch, PREG_OFFSET_CAPTURE, $nextTag)) {

            $tagClosure = '</'.$tagMatch[1][0].'>';
            $tagLength = stripos($this->_content, $tagClosure, $tagMatch[0][1])-$tagMatch[0][1]+strlen($tagClosure);

            $next = 0;
            $tagHtml = substr($this->_content, $tagMatch[0][1], $tagLength);
            $trArr = array();

            while (preg_match('#'.$this->_tokenRegex.'#i',
                $tagHtml, $m, PREG_OFFSET_CAPTURE, $next)) {

                $trArr[] = '{shown:\''.htmlspecialchars($m[1][0]).'\','
                    .'translated:\''.htmlspecialchars($m[2][0]).'\','
                    .'original:\''.htmlspecialchars($m[3][0]).'\','
                    .'scope:\''.htmlspecialchars($m[4][0]).'\'}';

                $tagHtml = substr_replace($tagHtml, $m[1][0], $m[0][1], strlen($m[0][0]));

                $next = $m[0][1];
            }
            if (!empty($trArr)) {
                $tag = strtolower($tagMatch[1][0]);
                switch ($tag) {
                    case 'script': case 'title':
                        $tagHtml .= '<span class="translate-inline-'.$tag.'" translate="['.join(',',$trArr).']">'.strtoupper($tag).'</span>';
                        break;
                }

                $this->_content = substr_replace($this->_content, $tagHtml, $tagMatch[0][1], $tagLength);

                switch ($tag) {
                    case 'select': case 'button':
                        $this->_content = substr_replace($this->_content, ' translate="['.join(',',$trArr).']"', $tagMatch[2][1], 0);
                        break;
                }
            }

            $nextTag = $tagMatch[0][1]+10;
        }
    }

    protected function _otherText()
    {
        $next = 0;
        while (preg_match('#('.$this->_tokenRegex.')(.|$)#',
            $this->_content, $m, PREG_OFFSET_CAPTURE, $next)) {

            $tr = '{shown:\''.htmlspecialchars($m[2][0]).'\','
                .'translated:\''.htmlspecialchars($m[3][0]).'\','
                .'original:\''.htmlspecialchars($m[4][0]).'\','
                .'scope:\''.htmlspecialchars($m[5][0]).'\'}';
            $spanHtml = '<span translate="['.$tr.']">'.$m[2][0].'</span>';

            $this->_content = substr_replace($this->_content, $spanHtml, $m[0][1], $m[6][1]-$m[0][1]);
            $next = $m[0][1];
        }
    }


}