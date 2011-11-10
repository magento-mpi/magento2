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
 * @package     Enterprise_CustomerSegment
 * @copyright   Copyright (c) 2010 Magento Inc. (http://www.magentocommerce.com)
 * @license     http://www.magentocommerce.com/license/enterprise-edition
 */

/**
 * Orders conditions options group
 */
class Enterprise_CustomerSegment_Model_Segment_Condition_Sales
    extends Enterprise_CustomerSegment_Model_Condition_Abstract
{
    /**
     * Class constructor
     */
    public function __construct()
    {
        parent::__construct();
        $this->setType('Enterprise_CustomerSegment_Model_Segment_Condition_Sales');
        $this->setValue(null);
    }

    /**
     * Get condition "selectors"
     *
     * @return array
     */
    public function getNewChildSelectOptions()
    {
        return array(
            'value' => array(
                array( // order address combo
                    'value' => 'Enterprise_CustomerSegment_Model_Segment_Condition_Order_Address',
                    'label' => Mage::helper('Enterprise_CustomerSegment_Helper_Data')->__('Order Address')),
                array(
                    'value' => 'Enterprise_CustomerSegment_Model_Segment_Condition_Sales_Salesamount',
                    'label' => Mage::helper('Enterprise_CustomerSegment_Helper_Data')->__('Sales Amount')),
                array(
                    'value' => 'Enterprise_CustomerSegment_Model_Segment_Condition_Sales_Ordersnumber',
                    'label' => Mage::helper('Enterprise_CustomerSegment_Helper_Data')->__('Number of Orders')),
                array(
                    'value' => 'Enterprise_CustomerSegment_Model_Segment_Condition_Sales_Purchasedquantity',
                    'label' => Mage::helper('Enterprise_CustomerSegment_Helper_Data')->__('Purchased Quantity')),
             ),
            'label' => Mage::helper('Enterprise_CustomerSegment_Helper_Data')->__('Sales')
        );
    }
}
