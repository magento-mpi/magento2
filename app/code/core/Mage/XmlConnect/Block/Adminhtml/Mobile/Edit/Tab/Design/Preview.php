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
class Mage_XmlConnect_Block_Adminhtml_Mobile_Edit_Tab_Design_Preview extends Mage_Core_Block_Template
{
    public function __construct()
    {
        parent::__construct();
        $this->setTemplate('xmlconnect/preview.phtml');
    }

    protected function _beforeToHtml()
    {
        $model = Mage::registry('current_app');
        $this->setThemeImage(Mage::getBaseUrl('skin') . 'xmlconnect/empty.png');
        if ($model) {
            $formData = $model->getFormData();
            if (isset($formData['conf[extra][theme]'])) {
                $index = $formData['conf[extra][theme]'];
                $themes = Mage::helper('xmlconnect/data')->getThemes();
                if (isset($themes[$index])) {
                    $theme = $themes[$index];
                    $this->setThemeImage($theme->getPreviewUrl());
                }
            }
        }
        return parent::_beforeToHtml();
    }
}
