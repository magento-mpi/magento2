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

/**
 * XmlConnect index controller
 *
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
//        $this->loadLayout(false);
//        $this->renderLayout();
        $xml = '<?xml version="1.0" encoding="UTF-8"?>
            <product id="10">
                <options>
                    <!--Simple product options (Qty\'s are not editable)-->
                    <option code="option_code1" type="text" label="Text Option Is Not Required" is_qty_editable="0" price="$5.00"/>
                    <option code="option_code1" type="text" label="Text Option Is Required" is_qty_editable="0" is_required="1" price="$5.00"/>
                    <option code="option_code2" type="select" label="Select Option With Specific Prices Per Option And Is Required" is_qty_editable="0" is_required="1">
                        <value code="value_1" label="Value Labe1" price="$7.00"/>
                        <value code="value_2" label="Value Labe2" price="$5.00"/>
                        <value code="value_3" label="Value Labe3" price="-$1.00"/>
                    </option>
                    <option code="option_code2" type="select" label="Select Option With One Price For Any Options And Is Not Required" is_qty_editable="0" price="$10.00">
                        <value code="value_1" label="Value Labe1"/>
                        <value code="value_2" label="Value Labe2"/>
                        <value code="value_3" label="Value Labe3"/>
                    </option>
                    <option code="option_code3" type="checkbox" label="CheckBox Option With Specific Prices Per Option And Is Required" is_qty_editable="0" is_required="1">
                        <value code="value_1" label="Value Labe1" price="$7.00"/>
                        <value code="value_2" label="Value Labe2" price="-$5.00"/>
                        <value code="value_3" label="Value Labe3" price="$1.00"/>
                    </option>
                    <option code="option_code4" type="checkbox" label="CheckBox Option With One Price For Any Options And Is Not Required" is_qty_editable="0" price="-$5.00">
                        <value code="value_code1" label="Value Labe1l"/>
                        <value code="value_code2" label="Value Label2"/>
                        <value code="value_code3" label="Value Label3"/>
                    </option>
                    <option code="option_code41" type="select" label="Radio Option With Specific Prices Per Option And Is Required" is_qty_editable="0" is_required="1">
                        <value code="value_code1" label="Value Labe1l" price="+$10.00"/>
                        <value code="value_code2" label="Value Label2" price="-$50.00"/>
                        <value code="value_code3" label="Value Label3" price="+$50.00"/>
                    </option>

                    <!--Configurable product options, can be "select" with relation on other options or can be any simple product options (Qty\'s are not editable)-->
                    <option code="option_code5_related" type="select" label="Related And Required Select Option (Brand Related on Size)" is_qty_editable="0" is_requred="1">
                        <value code="value_samsung_code" label="Samsung" price="$5.00">
                            <relation to="option_code6_relative">
                                <value code="small" label="Small" price="$5.00">
                                    <relation to="option_code7_relative">
                                        <value code="red" label="Red" price="$3.00">
                                            <relation to="option_code8_relative">
                                                <value code="good" label="Good" price="$13.00"/>
                                                <value code="bad" label="Bad" price="$42.00"/>
                                            </relation>
                                        </value>
                                        <value code="green" label="Green" price="$4.00">
                                            <relation to="option_code8_relative">
                                                <value code="good" label="Good" price="$13.00"/>
                                                <value code="best" label="Best" price="$22.00"/>
                                            </relation>
                                        </value>
                                    </relation>
                                </value>
                                <value code="medium" label="Medium" price="$5.00">
                                    <relation to="option_code7_relative">
                                        <value code="red" label="Red" price="$6.00">
                                            <relation to="option_code8_relative">
                                                <value code="good" label="Good" price="$23.00"/>
                                                <value code="best" label="Best" price="$12.00"/>
                                                <value code="bad" label="Bad" price="$143.00"/>
                                            </relation>
                                        </value>
                                        <value code="green" label="Green" price="$7.00"/>
                                    </relation>
                                </value>
                                <value code="large" label="Large" price="$5.00">
                                    <relation to="option_code7_relative">
                                        <value code="blue" label="Blue" price="$8.00">
                                            <relation to="option_code8_relative">
                                                <value code="best" label="Best" price="$12.00"/>
                                                <value code="bad" label="Bad" price="$143.00"/>
                                            </relation>
                                        </value>
                                        <value code="green" label="Green" price="$9.00">
                                            <relation to="option_code8_relative">
                                                <value code="good" label="Good" price="$23.00"/>
                                                <value code="best" label="Best" price="$12.00"/>
                                                <value code="bad" label="Bad" price="$143.00"/>
                                            </relation>
                                        </value>
                                    </relation>
                                </value>
                            </relation>
                        </value>
                        <value code="value_htc_code" label="HTC" price="$5.00">
                            <relation to="option_code6_relative">
                                <value code="small" label="Small" price="$5.00">
                                    <relation to="option_code7_relative">
                                        <value code="red" label="Red" price="$6.00">
                                            <relation to="option_code8_relative">
                                                <value code="good" label="Good" price="$23.00"/>
                                                <value code="best" label="Best" price="$12.00"/>
                                                <value code="bad" label="Bad" price="$143.00"/>
                                            </relation>
                                        </value>
                                        <value code="green" label="Green" price="$7.00">
                                            <relation to="option_code8_relative">
                                                <value code="best" label="Best" price="$12.00"/>
                                                <value code="bad" label="Bad" price="$183.00"/>
                                            </relation>
                                        </value>
                                    </relation>
                                </value>
                                <value code="medium" label="Medium" price="$5.00">
                                    <relation to="option_code7_relative">
                                        <value code="yellow" label="Yellow" price="$14.00">
                                            <relation to="option_code8_relative">
                                                <value code="good" label="Good" price="$13.00"/>
                                                <value code="best" label="Best" price="$22.00"/>
                                                <value code="bad" label="Bad" price="$113.00"/>
                                            </relation>
                                        </value>
                                        <value code="green" label="Green" price="$7.00">
                                            <relation to="option_code8_relative">
                                                <value code="good" label="Good" price="$23.00"/>
                                                <value code="best" label="Best" price="$12.00"/>
                                            </relation>
                                        </value>
                                    </relation>
                                </value>
                            </relation>
                        </value>
                        <value code="value_vaio_code" label="VAIO" price="$10.00">
                            <relation to="option_code6_relative">
                                <value code="medium" label="Medium" price="$5.00">
                                    <relation to="option_code7_relative">
                                        <value code="yellow" label="Yellow" price="$14.00">
                                            <relation to="option_code8_relative">
                                                <value code="bad" label="Bad" price="$43.00"/>
                                            </relation>
                                        </value>
                                        <value code="green" label="Green" price="$7.00">
                                            <relation to="option_code8_relative">
                                                <value code="good" label="Good" price="$113.00"/>
                                                <value code="bad" label="Bad" price="$13.00"/>
                                            </relation>
                                        </value>
                                    </relation>
                                </value>
                                <value code="large" label="Large" price="$5.00">
                                    <relation to="option_code7_relative">
                                        <value code="black" label="Black" price="$18.00">
                                            <relation to="option_code8_relative">
                                                <value code="good" label="Good" price="$23.00"/>
                                                <value code="best" label="Best" price="$12.00"/>
                                                <value code="bad" label="Bad" price="$143.00"/>
                                            </relation>
                                        </value>
                                        <value code="green" label="Green" price="$17.00">
                                            <relation to="option_code8_relative">
                                                <value code="good" label="Good" price="$23.00"/>
                                                <value code="bad" label="Bad" price="$143.00"/>
                                            </relation>
                                        </value>
                                    </relation>
                                </value>
                            </relation>
                        </value>
                    </option>
                    <option code="option_code6_relative" type="select" label="Relative On Brand Size Option And Is Required" is_qty_editable="0" is_requred="1"/>
                    <option code="option_code7_relative" type="select" label="Relative On Size Color Option And Is Required" is_qty_editable="0" is_requred="1"/>
                    <option code="option_code8_relative" type="select" label="Relative On Color Quality Option And Is Required" is_qty_editable="0" is_requred="1"/>

                    <option code="option_code7" type="text" label="Text Option Is Not Required" is_qty_editable="0" price="$5.00"/>
                    <option code="option_code8" type="select" label="Select Option With One Price For Any Options And Is Not Required" is_qty_editable="0" price="$10.00">
                        <value code="value_1" label="Value Labe1"/>
                        <value code="value_2" label="Value Labe2"/>
                        <value code="value_3" label="Value Labe3"/>
                    </option>
                    <option code="option_code9" type="checkbox" label="CheckBox Option With Specific Prices Per Option And Is Required" is_qty_editable="0" is_required="1">
                        <value code="value_1" label="Value Labe1" price="$7.00"/>
                        <value code="value_2" label="Value Labe2" price="$5.00"/>
                        <value code="value_3" label="Value Labe3" price="$1.00"/>
                    </option>
                    <option code="option_code91" type="select" label="Radio Option With One Price For Any Options And Is Required" is_qty_editable="0" price="$14.00" is_required="1">
                        <value code="value_code1" label="Value Labe1l"/>
                        <value code="value_code2" label="Value Label2"/>
                        <value code="value_code3" label="Value Label3"/>
                    </option>

                    <!--Grouped product options (Qty\'s can be editable or not)-->
                    <option code="option_code10" type="product" label="Option With Editable Qty And Without Qty Preset" is_qty_editable="1" price="$10.00"/>
                    <option code="option_code11" type="product" label="Option With Not Editable Qty And With Qty Preset" is_qty_editable="0" qty="1" price="$1.00"/>
                    <option code="option_code12" type="product" label="Option With Editable Qty With Qty Preset" is_qty_editable="1" qty="6" price="$12.00"/>

                    <!--Bundle product options (Qty\'s can be editable or not)-->
                    <option code="option_code13" type="text" lable="Text Option" is_qty_editable="0" price="$5"/>

                    <option code="option_code14" type="select" lable="Select Option" is_qty_editable="0" qty="2">
                        <value code="value_code" label="Value Label" price="$7"/>
                    </option>
                    <option code="option_code15" type="select" lable="Radio Option" is_qty_editable="1" qty="2" price="-$10">
                        <value code="value_code1" label="Value Labe1l"/>
                        <value code="value_code2" label="Value Label2"/>
                    </option>
                    <option code="option_code16" type="select" lable="Radio Option 2" is_qty_editable="1" is_required="1">
                        <value code="value_code1" label="Value Labe1l" price="+$3.00"/>
                        <value code="value_code2" label="Value Label2" price="+$8.00"/>
                    </option>
                    <option code="option_code17" type="checkbox" label="CheckBox Option" is_qty_editable="1" is_required="1">
                        <value code="value_1" label="Value Labe1" price="+$7.00"/>
                        <value code="value_2" label="Value Labe2" price="+$5.00"/>
                        <value code="value_3" label="Value Labe3" price="+$1.00"/>
                    </option>

                    <!--Gift Card options (Qty\'s are not editable, prices are not set)-->
                    <option code="option_code19" type="text" lable="Text Option 1" is_qty_editable="0"/>
                    <option code="option_code20" type="text" lable="Text Option 2" is_qty_editable="0" is_required="1"/>
                    <option code="option_code21" type="select" label="Amount" is_qty_editable="0">
                        <value code="value_code1" label="$100.00"/>
                        <value code="value_code2" label="$200.00"/>
                        <value code="value_code3" label="$300.00"/>
                    </option>
                </options>
            </product>
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

    public function galleryAction()
    {
        $this->loadLayout(false);
        $this->renderLayout();
    }
}