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
    protected $_tokenRegex = '<<<(.*)>><<(.*)>><<(.*)>><<(.*)>>>';
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
            <link rel="stylesheet" type="text/css" href="'.$baseJsUrl.'prototype/windows/themes/darkX.css"/>
        ';

        $ajaxUrl = Mage::getUrl('core/ajax/translate');

        $bodyArray[] = <<<EOT
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

function translateInlineObserve(event) {
    Event.stop(event);
    var el = Event.element(event);
    eval('var data = '+el.getAttribute('translate'));

    var content = '<table cellspacing="2" style="width:100%; margin:10px;">';
    var t = new Template(
        '<tr><td class="label">Scope: </td><td class="value">#{scope}</td></tr>'+
        '<tr><td class="label">Shown: </td><td class="value">#{shown_escape}</td></tr>'+
        '<tr><td class="label">Original: </td><td class="value">#{original_escape}</td></tr>'+
        '<tr><td class="label">Translated: </td><td class="value">#{translated_escape}</td></tr>'+
        '<tr><td class="label">Custom: </td><td class="value">'+
            '<input name="translate[#{i}][original]" type="hidden" value=#{scope}::#{original_escape}"/>'+
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
    content += '</table>';

    Dialog.confirm(content, {
        draggable:true,
        resizable:true,
        closable:true,
        className:"darkX",
        title:"Translation",
        width:500,
        height:400,
        recenterAuto:false,
        hideEffect:Element.hide,
        showEffect:Element.show,
        id:"translate-inline",
        buttonClass:"form-button",
        okLabel:"Submit",
        ok: function(win) {
            Ajax.Request('{$ajaxUrl}', {
                method:'post',
                parameters:{test:'test'}
            });
            win.close();
        }
    });
}
$$('*[translate]').each(function(el){
    el.addClassName('translate-inline');
    Event.observe(el, 'mousedown', translateInlineObserve);
});

</script>
</body>
EOT;
    }


    protected function _tagAttributes()
    {
        $nextTag = 0;
        while (preg_match('#<([a-z]+)[^>]+(('.$this->_tokenRegex.')[^/>]*)+/?(>)#Ui',
            $this->_content, $tagMatch, PREG_OFFSET_CAPTURE, $nextTag)) {
#echo "<xmp>"; print_r($tagMatch); echo "</xmp>"; exit;

            $next = 0;
            $tagHtml = $tagMatch[0][0];
            $trArr = array();

            while (preg_match('#(([a-z]+)=[\'"])('.$this->_tokenRegex.')([\'"])#Ui',
                $tagHtml, $m, PREG_OFFSET_CAPTURE, $next)) {

                $trArr[] = '{attribute:\''.htmlspecialchars($m[2][0]).'\','
                    .'shown:\''.htmlspecialchars($m[4][0]).'\','
                    .'translated:\''.htmlspecialchars($m[5][0]).'\','
                    .'original:\''.htmlspecialchars($m[6][0]).'\','
                    .'scope:\''.htmlspecialchars($m[7][0]).'\'}';
                $tagHtml = substr_replace($tagHtml, $m[4][0], $m[3][1], $m[8][1]-$m[3][1]);
                $next = $m[0][1];
            }

            $trAttr = ' translate="['.join(',', $trArr).']"';
            $tagHtml = preg_replace('#/?>$#', $trAttr.'$0', $tagHtml);

            $this->_content = substr_replace($this->_content, $tagHtml, $tagMatch[0][1], $tagMatch[8][1]+1-$tagMatch[0][1]);
            $nextTag = $tagMatch[0][1];
        }
#echo "<xmp>"; print_r($this->_content); echo "</xmp>"; exit;
    }

    protected function _specialTags()
    {
        $nextTag = 0;

        while (preg_match('#<(script|select)[^>]+(>)#Ui',
            $this->_content, $tagMatch, PREG_OFFSET_CAPTURE, $nextTag)) {

            $tagClosure = '</'.$tagMatch[1][0].'>';
            $tagLength = stripos($this->_content, $tagClosure, $tagMatch[0][1])-$tagMatch[0][1]+strlen($tagClosure);

            $next = 0;
            $tagHtml = substr($this->_content, $tagMatch[0][1], $tagLength);
            $trArr = array();

            while (preg_match('#'.$this->_tokenRegex.'#Ui',
                $tagHtml, $m, PREG_OFFSET_CAPTURE, $next)) {

                $trArr[] = '{shown:\''.htmlspecialchars($m[1][0]).'\','
                    .'translated:\''.htmlspecialchars($m[2][0]).'\','
                    .'original:\''.htmlspecialchars($m[3][0]).'\','
                    .'scope:\''.htmlspecialchars($m[4][0]).'\'}';

                $tagHtml = substr_replace($tagHtml, $m[1][0], $m[0][1], strlen($m[0][0]));

                $next = $m[0][1];
            }
            if (!empty($trArr)) {
                if (strtolower($tagMatch[1][0])==='script') {
                    $tagHtml .= '<span translate="['.join(',',$trArr).']">SCRIPT</span>';
                }

                $this->_content = substr_replace($this->_content, $tagHtml, $tagMatch[0][1], $tagLength);

                if (strtolower($tagMatch[1][0])==='select') {
                    $this->_content = substr_replace($this->_content, ' translate="['.join(',',$trArr).']"', $tagMatch[2][1], 0);
                }
            }

            $nextTag = $tagMatch[0][1]+10;
        }
    }

    protected function _otherText()
    {
//        return;
//        $this->_content = preg_replace('#'.$this->_tokenRegex.'#Uu', '$1', $this->_content);
//        return;

        $next = 0;
        while (preg_match('#('.$this->_tokenRegex.')(.|$)#U',
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
//    protected function _tagsInnerHtml()
//    {
//        return;
//        $nextTag = 0;
//        while (preg_match('#<([a-z]+)\s*([^>]*)(>)[^<>]*('.$this->_tokenRegex.'[^/>]*)+</[a-z]+\s*(>)#Uuim',
//            $this->_content, $tagMatch, PREG_OFFSET_CAPTURE, $nextTag)) {
//
//#echo "<xmp>".print_r($tagMatch,1)."</xmp><hr>";
//            $next = 0;
//            $tagHtml = $tagMatch[0][0];
//            $trArr = array();
//
//            while (preg_match('#('.$this->_tokenRegex.')(.|$)#Uuim',
//                $tagHtml, $m, PREG_OFFSET_CAPTURE, $next)) {
//
//                $tr = '{translated:\''.htmlspecialchars($m[3][0]).'\','
//                    .'original:\''.htmlspecialchars($m[4][0]).'\','
//                    .'scope:\''.htmlspecialchars($m[5][0]).'\'}';
//
//                switch ($tagMatch[1][0]) {
//                    case 'script':
//                    case 'option':
//                        $trArr[] = $tr;
//                        $replace = $m[2][0];
//                        break;
//
//                    default:
//                        $replace = '<span translate="['.$tr.']">'.$m[2][0].'</span>';
//                }
//                $tagHtml = substr_replace($tagHtml, $replace, $m[1][1], $m[6][1]-$m[1][1]);
//
//                $next = $m[0][1];
//            }
//
//            if (!empty($trArr)) {
//                $attrHtml = $tagMatch[2][0];
//                if (preg_match('#^(.* translate="\[)(.*)(\]")$#i', $attrHtml, $m)) {
//                    $attrHtml = $m[1].$m[2].','.join(',', $trArr).$m[3];
//                } else {
//                    $attrHtml .= ' translate="['.join(',', $trArr).']"';
//                }
//                $tagHtml = str_replace($tagMatch[2][0], $attrHtml, $tagHtml);
//            }
//#echo "<xmp>"; print_r($tagHtml); echo "</xmp><hr>";
//
//            $this->_content = substr_replace($this->_content, $tagHtml, $tagMatch[0][1], $tagMatch[9][1]+1-$tagMatch[0][1]);
//            $nextTag = $tagMatch[0][1];
//        }
//#exit;
//#echo "<xmp>"; print_r($this->_content); echo "</xmp>"; exit;
//    }


}