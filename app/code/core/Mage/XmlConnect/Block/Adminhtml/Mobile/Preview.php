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
class Mage_XmlConnect_Block_Adminhtml_Mobile_Preview extends Mage_Core_Block_Template
{
   /**
    * path to preview media folder
    *
    * @var string
    */
    const XMLCONNECT_MEDIA_PREVIEW = 'xmlconnect/mobile-preview/';

    public function __construct()
    {
        parent::__construct();
        $this->setTemplate('xmlconnect/preview_iframe.phtml');
    }

    public function setConf($conf)
    {
        $tabs = $conf['tabBar']['tabs'];
        foreach ($tabs->getEnabledTabs() as $tab) {
            $conf['tabBar'][$tab->action]['label'] = $tab->label;
            $conf['tabBar'][$tab->action]['image'] =
                Mage::getBaseUrl('skin') . 'xmlconnect/' . $tab->image;
        }
        parent::setConf($conf);
    }

   /**
    * Returns url for skin folder
    *
    * @param string $name  - file name
    *
    * @return string
    */
    protected function getPreviewUrl($name = '')
    {
        if (!isset($this->_skinFolder)) {
            $this->_skinFolder = Mage::getBaseUrl('skin');
        }
        return  $this->_skinFolder . self::XMLCONNECT_MEDIA_PREVIEW . $name;
    }

}
