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
 * @package     Mage_Sales
 * @copyright   Copyright (c) 2010 Magento Inc. (http://www.magentocommerce.com)
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */
class Mage_Sales_Block_Order_Creditmemo_Comments extends Mage_Core_Block_Template
{
    /**
     * Current creditmemo instance
     *
     * @var Mage_Sales_Model_Order_Creditmemo
     */
    protected $_creditmemo;

    /**
     * Currect creditmemo comments collection
     *
     * @var Mage_Sales_Model_Mysql4_Order_Creditmemo_Comment_Collection
     */
    protected $_commentCollection;

    /**
     * Initialize order creditmemo comments
     *
     * @return Mage_Sales_Model_Mysql4_Order_Creditmemo_Comment_Collection
     */
    public function getComments()
    {
        if (is_null($this->_commentCollection)) {
            $this->_commentCollection = Mage::getResourceModel('sales/order_creditmemo_comment_collection')
              ->setCreditmemoFilter($this->getCreditmemo()->getId())
              ->addVisibleOnFrontFilter();
        }
        return $this->_commentCollection;
    }

    /**
     * Get comments creditmemo
     *
     * @return Mage_Sales_Model_Order_Creditmemo
     */
    public function getCreditmemo()
    {
        if ($this->_creditmemo === null) {
            if ($this->hasData('creditmemo')) {
                $this->_creditmemo = $this->_getData('creditmemo');
            } elseif (Mage::registry('current_creditmemo')) {
                $this->_creditmemo = Mage::registry('current_creditmemo');
            } elseif ($this->getParentBlock()->getCreditmemo()) {
                $this->_creditmemo = $this->getParentBlock()->getCreditmemo();
            }
        }
        return $this->_creditmemo;
    }

    /**
     * Sets comments creditmemo
     *
     * @return Mage_Sales_Block_Order_Creditmemo_Comments
     */
    public function setCreditmemo($creditmemo)
    {
        $this->_creditmemo = $creditmemo;
        return $this;
    }

    /**
     * Returns whether there are comments to show on frontend
     *
     * @return bool
     */
    public function hasComments()
    {
        return $this->getComments()->count() > 0;
    }
}