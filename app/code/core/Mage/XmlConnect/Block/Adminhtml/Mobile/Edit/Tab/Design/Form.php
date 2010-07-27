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
class Mage_XmlConnect_Block_Adminhtml_Mobile_Edit_Tab_Design_Form extends Mage_Core_Block_Abstract
{
    /**
     * Preparing form layout
     *
     * @return Mage_Core_Block_Abstract
     */
    protected function _prepareLayout()
    {
        $accordion = $this->getLayout()
            ->createBlock('adminhtml/widget_accordion')
            ->setId('mobile_edit_tab_design_accordion');

        $accordion->addItem('images', array(
            'title'   => Mage::helper('xmlconnect')->__('Images'),
            'content' => new Mage_XmlConnect_Block_Adminhtml_Mobile_Edit_Tab_Design_Accordion_Images,
            'open'    => true,
        ));

        $accordion->addItem('colors', array(
            'title'   => Mage::helper('xmlconnect')->__('Color Themes'),
            'content' => new Mage_XmlConnect_Block_Adminhtml_Mobile_Edit_Tab_Design_Accordion_Colors,
            'open'    => true,
        ));

//        $accordion->addItem('fonts', array(
//            'title'   => Mage::helper('xmlconnect')->__('Fonts'),
//            'content' => new Mage_XmlConnect_Block_Adminhtml_Mobile_Edit_Tab_Design_Accordion_Fonts,
//            'open'    => true,
//        ));
//
//        $accordion->addItem('advanced', array(
//            'title'   => Mage::helper('xmlconnect')->__('Advanced Settings'),
//            'content' => new Mage_XmlConnect_Block_Adminhtml_Mobile_Edit_Tab_Design_Accordion_Advanced,
//            'open'    => false,
//        ));

        $accordion->addItem('tabs', array(
            'title'   => Mage::helper('xmlconnect')->__('Tabs'),
            'content' => new Mage_XmlConnect_Block_Adminhtml_Mobile_Edit_Tab_Design_Accordion_Tabs,
            'open'    => true,
        ));

        $this->setChild('accordion', $accordion);

        return $this;
    }

    protected function _toHtml()
    {
        return $this->getChildHtml('accordion');
    }
}
