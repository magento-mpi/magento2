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
 * @package     Mage_Adminhtml
 * @copyright   Copyright (c) 2009 Irubin Consulting Inc. DBA Varien (http://www.varien.com)
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */


/**
 * System congifuration shipping methods allow all countries selec
 *
 * @category   Mage
 * @package    Mage_Adminhtml
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Enterprise_SalesPool_Block_Adminhtml_System_Config_Form_Field_Allow_Method extends Enterprise_Enterprise_Block_Adminhtml_System_Config_Form_Field
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
