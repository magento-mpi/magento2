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
 * @package     Mage_Rss
 * @copyright   Copyright (c) 2009 Irubin Consulting Inc. DBA Varien (http://www.varien.com)
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * XmlConnect index controller
 *
 * @file        IndexController.php
 * @author      Magento Core Team <core@magentocommerce.com>
 */

class Mage_XmlConnect_IndexController extends Mage_Core_Controller_Front_Action
{
    public function preDispatch()
    {
        parent::preDispatch();
        $this->getResponse()->setHeader('Content-type', 'text/xml; charset=UTF-8');
    }

     public function indexAction()
    {
        $this->loadLayout(false);
        $this->renderLayout();
    }

    public function categoryAction()
    {
        $this->loadLayout(false);
        $this->renderLayout();
    }

    public function filtersAction()
    {
        $this->loadLayout(false);
        $this->renderLayout();
    }

    public function productAction()
    {
        $this->loadLayout(false);
        $this->renderLayout();
    }
    
    public function optionsAction()
    {
        $xml = '<?xml version="1.0" encoding="UTF-8"?>
            <options>
                <option code="option_code1" type="text" label="Text Option" is_qty_editable="0" price="$5.00"/>
                <option code="option_code1" type="text" label="Text Option" is_qty_editable="1" />
                <option code="option_code2" type="select" label="Select Option" is_required="1" is_qty_editable="0">
                    <value code="value_1" label="Value Labe1" price="$7.00"/>
                    <value code="value_2" label="Value Labe2" price="$5.00"/>
                    <value code="value_3" label="Value Labe3" price="$1.00"/>
                </option>
                <option code="option_code3" type="checkbox" label="CheckBox Option" is_qty_editable="0" price="$10.00">
                    <value code="value_code1" label="Value Labe1l"/>
                    <value code="value_code2" label="Value Label2"/>
                    <value code="value_code3" label="Value Label3"/>
                </option>
                
                <option code="option_code4_related" type="select" label="Color" is_qty_editable="0">
                    <value code="value_red_code" label="Red" price="+$5.00">
                        <relation to="option_code5_relative">
                            <value code="small" label="Small"/>
                            <value code="big" label="Big"/>
                        </relation>
                    </value>
                    <value code="value_green_code" label="Green">
                        <relation to="option_code5_relative">
                            <value code="middle" label="Middle"/>
                            <value code="big" label="Big"/>
                        </relation>
                    </value>
                    <value code="value_black_code" label="Black">
                        <relation to="option_code5_relative">
                            <value code="middle" label="Middle"/>
                            <value code="small" label="Small"/>
                            <value code="big" label="Big"/>
                        </relation>
                    </value>
                </option>
                <option code="option_code5_relative" type="select" label="Size" is_qty_editable="0"/>

                <option code="option_code3" type="product" label="Option with qty" is_qty_editable="1" qty="2" price="$10.00"/>
                <option code="option_code4" type="product" label="Option with qty" is_qty_editable="1" qty="3" price="$1.00"/>
                <option code="option_code5" type="product" label="Option with qty" is_qty_editable="1" qty="6" price="$12.00"/>

                <option code="option_code7" type="select" label="Select Option" is_qty_editable="0">
                    <value code="value_code1" label="Value Labe1l" price="+$100.00"/>
                    <value code="value_code2" label="Value Label2" price="+$50.00"/>
                    <value code="value_code3" label="Value Label3" price="+$10.00"/>
                </option>
                <option code="option_code8" type="select" label="Select Option+qty" is_qty_editable="1">
                    <value code="value_code1" label="Value Labe1l with qty" price="+$100.00"/>
                    <value code="value_code2" label="Value Label2 with qty" price="+$50.00"/>
                    <value code="value_code3" label="Value Label3 with qty" price="+$10.00"/>
                </option>
                <option code="option_code9" type="checkbox" label="Checkbox Option" is_qty_editable="1">
                    <value code="value_code1" label="Value Labe1l with qty" price="+$100.00"/>
                    <value code="value_code2" label="Value Label2 with qty" price="+$50.00"/>
                    <value code="value_code3" label="Value Label3 with qty" price="+$10.00"/>
                </option>
                <option code="option_code10" type="checkbox" label="Checkbox Option" is_qty_editable="0">
                    <value code="value_code1" label="Value Labe1l" price="+$100.00"/>
                    <value code="value_code2" label="Value Label2" price="+$50.00"/>
                    <value code="value_code3" label="Value Label3" price="+$10.00"/>
                </option>
            </options>
        ';
        $this->getResponse()->setBody($xml);
    }

    public function reviewsAction()
    {
        $this->loadLayout(false);
        $this->renderLayout();
    }

    public function reviewAction()
    {
        $this->loadLayout(false);
        $this->renderLayout();
    }

}