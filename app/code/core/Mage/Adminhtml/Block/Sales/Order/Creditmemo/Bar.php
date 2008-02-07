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
 * Adminhtml creditmemo bar
 *
 * @category   Mage
 * @package    Mage_Adminhtml
 * @author     Alexander Stadnitski <alexander@varien.com>
 */

class Mage_Adminhtml_Block_Sales_Order_Creditmemo_Bar extends Mage_Core_Block_Template
{
    protected $_totals = array();
    protected $_grandTotal = array();

    protected function _construct()
    {
        parent::_construct();
        $this->setTemplate('sales/order/creditmemo/bar.phtml');
    }

    protected function getTotals()
    {
        return $this->_totals;
    }

    protected function getGrandTotal()
    {
        return $this->_grandTotal;
    }

    /**
     * Enter description here...
     *
     * @param string $label
     * @param float $value
     */
    public function addTotal($label, $value)
    {
        $this->_totals[] = array('label' => $label, 'value' => $value);
        return $this;
    }

    public function setGrandTotal($label, $value)
    {
        $this->_grandTotal = array(
            'label' => $label,
            'value' => $value,
        );

        return $this;
    }
}