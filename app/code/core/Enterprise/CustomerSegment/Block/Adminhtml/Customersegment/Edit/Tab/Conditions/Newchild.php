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
 * @category   Enterprise
 * @package    Enterprise_CustomerSegment
 * @copyright  Copyright (c) 2008 Irubin Consulting Inc. DBA Varien (http://www.varien.com)
 * @license    http://www.magentocommerce.com/license/enterprise-edition
 */

class Enterprise_CustomerSegment_Block_Adminhtml_Customersegment_Edit_Tab_Conditions_Newchild extends Mage_Adminhtml_Block_Template implements Varien_Data_Form_Element_Renderer_Interface
{
    /**
     * element for render
     */
    protected $_element;

    /**
     * Intialize block
     *
     * @return void
     */    
    protected function _construct()
    {
        $this->setTemplate('enterprise/customersegment/edit/conditions/newchild.phtml');
    }

    /**
     * Return element for render
     *
     * @return Varien_Data_Form_Element_Abstract
     */    
    public function getElement()
    {
        return $this->_element;
    }

    /**
     * Return element
     * 
     * @param Varien_Data_Form_Element_Abstract $element
     * @return string
     */    
    public function render(Varien_Data_Form_Element_Abstract $element)
    {
        $element->addClass('element-value-changer');
        $this->_element = $element;
        return $this->toHtml();
    }
}