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
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade Magento to newer
 * versions in the future. If you wish to customize Magento for your
 * needs please refer to http://www.magentocommerce.com for more information.
 *
 * @category    Mage
 * @package     Mage_XmlConnect
 * @copyright   Copyright (c) 2009 Irubin Consulting Inc. DBA Varien (http://www.varien.com)
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

class Mage_XmlConnect_Block_Adminhtml_Mobile_Helper_Image extends Varien_Data_Form_Element_Image
{
    protected function _getUrl()
    {
        $url = false;
        if ($this->getValue()) {
            if (strpos($this->getValue(), '://') === FALSE ) {
                $url = Mage::getBaseUrl('media').'xmlconnect/'.$this->getValue();
            } else {
                $url = $this->getValue();
            }
        }
        return $url;
    }

    public function getUploadName()
    {
        /**
         * Ugly hack to avoid $_FILES[..]['name'][..][..]
         */
        $name = $this->getName();
        $name = strtr($name, array('[' => '/', ']' => ''));
        return $name;
    }

    public function getElementHtml()
    {
        $html = '';

        if ($this->getValue()) {
            $url = $this->_getUrl();
            $html = '<a href="'.$url.'" onclick="imagePreview(\''.$this->getHtmlId().'_image\'); return false;"><img src="'.$url.'" id="'.$this->getHtmlId().'_image" title="'.$this->getValue().'" alt="'.$this->getValue().'" height="22" width="22" class="small-image-preview v-middle" /></a> ';
        }

        $html .= '<input id="'.$this->getHtmlId().'_hidden" name="'.$this->getName()
             .'" value="'.$this->getEscapedValue().'" type="hidden" />';

        $this->setClass('input-file');
        $html .= '<input id="'.$this->getHtmlId().'" name="'.$this->getUploadName()
             .'" value="'.$this->getEscapedValue().'" '.$this->serialize($this->getHtmlAttributes()).'/>'."\n";
        $html.= $this->getAfterElementHtml();

        return $html;
    }

}
