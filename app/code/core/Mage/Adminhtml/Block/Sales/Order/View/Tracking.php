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
 * @category   Mage
 * @package    Mage_Adminhtml
 * @copyright  Copyright (c) 2004-2007 Irubin Consulting Inc. DBA Varien (http://www.varien.com)
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * Edit order giftmessage block
 *
 * @category   Mage
 * @package    Mage_Adminhtml
 * @author     Yuriy Scherbina <yuriy@varien.com>
 */
class Mage_Adminhtml_Block_Sales_Order_View_Tracking extends Mage_Adminhtml_Block_Widget
{

    public function __construct()
    {
        parent::__construct();
        $this->setTemplate('sales/order/view/tracking.phtml');
    }

    /**
     * Prepares layout of block
     *
     * @return Mage_Adminhtml_Block_Sales_Order_View_Giftmessage
     */
    protected function _prepareLayout()
    {
        $onclick = "submitAndReloadArea($('order_tracking_info').parentNode, '".$this->getSubmitUrl()."')";
        $this->setChild('save_button',
            $this->getLayout()->createBlock('adminhtml/widget_button')
                ->setData(array(
                    'label'   => $this->__('Add'),
                    'class'   => 'save',
                    'onclick' => $onclick
                ))

        );

        return $this;
    }

    public function getOrder()
    {
        return Mage::registry('sales_order');
    }

    /**
     * Retrieve save url
     *
     * @return string
     */
    public function getSubmitUrl()
    {
        return $this->getUrl('*/*/addTracking/', array('order_id'=>$this->getOrder()->getId()));
    }

    /**
     * Retrive save button html
     *
     * @return string
     */
    public function getSaveButtonHtml()
    {
        return $this->getChildHtml('save_button');
    }
} // Class Mage_Adminhtml_Block_Sales_Order_View_Tracking End