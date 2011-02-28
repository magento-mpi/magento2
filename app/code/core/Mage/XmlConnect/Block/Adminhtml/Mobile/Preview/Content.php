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
 * @copyright   Copyright (c) 2010 Magento Inc. (http://www.magentocommerce.com)
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */
class Mage_XmlConnect_Block_Adminhtml_Mobile_Preview_Content extends Mage_Adminhtml_Block_Template
{
    /**
     * Category item tint color styles
     *
     * @var string
     */
    protected $categoryItemTintColor = '';

    /**
     * Set path to template used for generating block's output.
     *
     * @param string $templateType
     * @return Mage_XmlConnect_Block_Adminhtml_Mobile_Preview_Content
     */
    public function setTemplate($templateType)
    {
        $deviceType = Mage::helper('xmlconnect')->getApplication()->getType();

        if ($deviceType == Mage_XmlConnect_Helper_Data::DEVICE_TYPE_IPHONE) {
            parent::setTemplate('xmlconnect/edit/tab/design/preview/' . $templateType . '.phtml');
        } else {
            parent::setTemplate('xmlconnect/edit/tab/design/preview/' . $templateType . '_' . $deviceType . '.phtml');
        }
        return $this;
    }

    /**
     * Prepare config data
     * Implement set "conf" data as magic method
     *
     * @param array $conf
     */
    public function setConf($conf)
    {
        if (!is_array($conf)) {
            $conf = array();
        }
        $tabs = isset($conf['tabBar']) && isset($conf['tabBar']['tabs']) ? $conf['tabBar']['tabs'] : false;
        if ($tabs !== false) {
            foreach ($tabs->getEnabledTabs() as $tab) {
                $tab = (array) $tab;
                $conf['tabBar'][$tab['action']]['label'] = $tab['label'];
                $conf['tabBar'][$tab['action']]['image'] =
                    Mage::helper('xmlconnect/image')->getSkinImagesUrl('mobile_preview/' . $tab['image']);
            }
        }
        $this->setData('conf', $conf);
    }

   /**
    * Get preview images url
    *
    * @param string $name - file name
    * @return string
    */
    public function getPreviewImagesUrl($name = '')
    {
        return  Mage::helper('xmlconnect/image')->getSkinImagesUrl('mobile_preview/' . $name);
    }


   /**
    * Retrieve url for images in the skin folder
    *
    * @param string $name - path to file name relative to the skin dir
    * @return string
    */
    public function getDesignPreviewImageUrl($name)
    {
        return Mage::helper('xmlconnect/image')->getSkinImagesUrl('design_default/' . $name);
    }

    /**
     * Get font info from config
     *
     * @param string $path
     * @return string
     */
    public function getConfigFontInfo($path)
    {
        return $this->getData('conf/fonts/' . $path);
    }

    public function getLogoUrl()
    {
        if ($this->getData('conf/navigationBar/icon')) {
            return $this->getData('conf/navigationBar/icon');
        } else {
            return $this->getDesignPreviewImageUrl($this->getInterfaceImagesPaths('conf/navigationBar/icon'));
        }
    }

    /**
     * Expose function getInterfaceImagesPaths from xmlconnect/images
     * Converts Data path(conf/submision/zzzz) to config path (conf/native/submission/zzzzz)
     *
     * @param string $path
     * @return array
     */
    public function getInterfaceImagesPaths($path)
    {
        $path = preg_replace('/^conf\/(.*)$/', 'conf/native/${1}', $path);
        return Mage::helper('xmlconnect/image')->getInterfaceImagesPaths($path);
    }

   /**
    * Get xmlconnect css url
    *
    * @param string $name - file name
    * @return string
    */
    public function getPreviewCssUrl($name = '')
    {
        return  Mage::getDesign()->getSkinUrl('xmlconnect/' . $name);
    }

    /**
     * Get category item tint color styles
     *
     * @return string
     */
    public function getCategoryItemTintColor()
    {
        if (!strlen($this->categoryItemTintColor)) {
            $percent = .4;
            $mask = 255;

            $hex = str_replace('#','',$this->getData('conf/categoryItem/tintColor'));
            $hex2 = '';
            $_rgb = array();

            $d = '[a-fA-F0-9]';

            if (preg_match("/^($d$d)($d$d)($d$d)\$/", $hex, $rgb)) {
                $_rgb = array(hexdec($rgb[1]), hexdec($rgb[2]), hexdec($rgb[3]));
            }
            if (preg_match("/^($d)($d)($d)$/", $hex, $rgb)) {
                $_rgb = array(hexdec($rgb[1] . $rgb[1]), hexdec($rgb[2] . $rgb[2]), hexdec($rgb[3] . $rgb[3]));
            }

            for ($i=0; $i<3; $i++) {
                $_rgb[$i] = round($_rgb[$i] * $percent) + round($mask * (1-$percent));
                if ($_rgb[$i] > 255) {
                    $_rgb[$i] = 255;
                }
            }

            for($i=0; $i < 3; $i++) {
                $hex_digit = dechex($_rgb[$i]);
                if(strlen($hex_digit) == 1) {
                    $hex_digit = "0" . $hex_digit;
                }
                $hex2 .= $hex_digit;
            }
            if($hex && $hex2){
                // for IE
                $this->categoryItemTintColor .= "filter: progid:DXImageTransform.Microsoft.gradient(startColorstr='#".$hex2."', endColorstr='#".$hex."');";
                // for webkit browsers
                $this->categoryItemTintColor .= "background:-webkit-gradient(linear, left top, left bottom, from(#".$hex2."), to(#".$hex."));";
                // for firefox
                $this->categoryItemTintColor .= "background:-moz-linear-gradient(top,  #".$hex2.",  #".$hex.");";
            }
        }
        return $this->categoryItemTintColor;
    }
}
