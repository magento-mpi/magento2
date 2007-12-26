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
        $this->setTemplate('sales/order/view/shippingtracking.phtml');
    }

    /**
     * Prepares layout of block
     *
     * @return Mage_Adminhtml_Block_Sales_Order_View_Giftmessage
     */
    protected function _prepareLayout()
    {
        $this->setChild('save_button',
            $this->getLayout()->createBlock('adminhtml/widget_button')
                ->setData(array(
                    'label'   => Mage::helper('shipping')->__('Save Tracking Number'),
                    'class'   => 'save',
                    'onclick' => 'trackingNumberController.saveTrackingNumber()'
                ))

        );

        return $this;
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

     /**
     * Set entity for form
     *
     * @param Varien_Object $entity
     * @return Mage_Adminhtml_Block_Sales_Order_View_Giftmessage
     */
    public function setEntity(Varien_Object $entity)
    {
        $this->_entity  = $entity;
        return $this;
    }

    /**
     * Retrive entity for form
     *
     * @return Varien_Object
     */
//    public function getEntity()
//    {
//        if(is_null($this->_entity)) {
//            $this->setEntity(Mage::getModel('sales/order')->getEntityModelByType('order'));
//            $this->getEntity()->load($this->getRequest()->getParam('entity'));
//        }
//        return $this->_entity;
//    }
//
//    /**
//     * Retrive block html id
//     *
//     * @return string
//     */
//    public function getHtmlId()
//    {
//        return 1;
//    }
//
//    public function getFieldName($name)
//    {
//        return 'trackingnumber[' . $this->getEntity()->getId() . '][' . $name . ']';
//    }
//
//    /**
//     * Retrive real html id for field
//     *
//     * @param string $name
//     * @return string
//     */
//    public function getFieldId($id)
//    {
//        return $this->getFieldIdPrefix() . $id;
//    }
//
//    /**
//     * Retrive field html id prefix
//     *
//     * @return string
//     */
//    public function getFieldIdPrefix()
//    {
//        return 'trackingnumber_' . $this->getEntity()->getId() . '_';
//    }


} // Class Mage_Adminhtml_Block_Sales_Order_View_Tracking End