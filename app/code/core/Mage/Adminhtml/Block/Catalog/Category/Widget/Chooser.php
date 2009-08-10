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
 * @category   Mage
 * @package    Mage_Adminhtml
 * @copyright  Copyright (c) 2008 Irubin Consulting Inc. DBA Varien (http://www.varien.com)
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * Category chooser for Wysiwyg CMS widget
 *
 * @category   Mage
 * @package    Mage_Adminhtml
 * @author     Magento Core Team <core@magentocommerce.com>
 */
class Mage_Adminhtml_Block_Catalog_Category_Widget_Chooser extends Mage_Adminhtml_Block_Template
{
    /**
     * Description goes here...
     *
     * @param none
     * @return void
     */
    public function prepareElementHtml(Varien_Data_Form_Element_Abstract $element)
    {
        $image = Mage::getDesign()->getSkinUrl('images/rule_chooser_trigger.gif');
        $chooserId = $element->getId() . 'product_chooser';
        $jsObject = 'oProduct' . $chooserId;
        $html = '
            <a href="javascript:void(0)" id="'.$chooserId.'" class="widget-option-chooser"><img src="'.$image.'" title="'.$this->helper('cms')->__('Open Chooser').'" /></a>
            <script type="text/javascript">
                '.$jsObject.' = new WysiwygWidget.optionCategory("'.$jsObject.'", "'.$this->getUrl('*/*/chooser', array('_current' => true)).'");
                Event.observe("'.$chooserId.'", "click", '.$jsObject.'.choose.bind('.$jsObject.'));
            </script>
        ';
        $element->setData('after_element_html',$html);
        return $element;
    }

}

