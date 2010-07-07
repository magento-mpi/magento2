<?php
/**
 * Magento Enterprise Edition
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Magento Enterprise Edition License
 * that is bundled with this package in the file LICENSE_EE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://www.magentocommerce.com/license/enterprise-edition
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
 * @category    Enterprise
 * @package     Enterprise_SalesPool
 * @copyright   Copyright (c) 2010 Magento Inc. (http://www.magentocommerce.com)
 * @license     http://www.magentocommerce.com/license/enterprise-edition
 */


/**
 * System configuration shipping methods allow all countries select
 *
 * @category    Enterprise
 * @package     Enterprise_SalesPool
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Enterprise_SalesPool_Block_Adminhtml_System_Config_Form_Field_Allow_Method extends Mage_Adminhtml_Block_System_Config_Form_Field
{

    /**
     * Enter description here...
     *
     * @param Varien_Data_Form_Element_Abstract $element
     * @return string
     */
    protected function _getElementHtml(Varien_Data_Form_Element_Abstract $element)
    {
        return $element->getElementHtml() . $this->getElementJS($element);
    }


    /**
     * Return javascript block for select all payment methods element
     *
     * @param Varien_Data_Form_Element_Abstract
     * @return string
     */
    public function getElementJS($element)
    {
        $javaScript = "
            <script type=\"text/javascript\">
                var {$element->getHtmlId()}_observer = function(evt){
                    var element;
                    if (Object.isString(evt)) {
                        element = $(evt);
                    } else {
                        element = Event.element(evt);
                    }

                    var methods = element.up('fieldset').down('.methods');
                    console.log(methods);
                    methods.disabled = parseInt(element.value) == 0;
                }
                document.observe('dom:loaded', function () {
                    {$element->getHtmlId()}_observer('{$element->getHtmlId()}');
                    Event.observe($('{$element->getHtmlId()}'), 'change', {$element->getHtmlId()}_observer);
                });
            </script>";
        return $javaScript;
    }


}
