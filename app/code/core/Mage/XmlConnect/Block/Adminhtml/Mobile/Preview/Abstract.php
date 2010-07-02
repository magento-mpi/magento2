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
class Mage_XmlConnect_Block_Adminhtml_Mobile_Preview_Abstract extends Mage_Core_Block_Template
{
   /**
    * path to preview media folder
    *
    * @var string
    */
    const XMLCONNECT_IMAGES_PREVIEW = 'images/xmlconnect/mobile-preview/';
    const XMLCONNECT_ADMIN_DEFAULT_IMAGES = 'images/xmlconnect/';

    public function setConf($conf)
    {
        $tabs = isset($conf['tabBar']) && isset($conf['tabBar']['tabs']) ? $conf['tabBar']['tabs'] : array();
        foreach ($tabs->getEnabledTabs() as $tab) {
            $conf['tabBar'][$tab->action]['label'] = $tab->label;
            $conf['tabBar'][$tab->action]['image'] =
                Mage::getDesign()->getSkinUrl(self::XMLCONNECT_IMAGES_PREVIEW . $tab->image);
        }
        parent::setConf($conf);
    }

   /**
    * Returns url for images in the skin folder
    *
    * @param string $name  - file name
    *
    * @return string
    */
    protected function getPreviewUrl($name = '')
    {
        return  Mage::getDesign()->getSkinUrl(self::XMLCONNECT_IMAGES_PREVIEW . $name);
    }

   /**
    * Returns url for skin css folder
    *
    * @param string $name  - file name
    *
    * @return string
    */
    protected function getPreviewCssUrl($name = '')
    {
        return  Mage::getDesign()->getSkinUrl('xmlconnect/' . $name);
    }
}
