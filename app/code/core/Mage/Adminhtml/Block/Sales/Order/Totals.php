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
 * Adminhtml order totals block
 *
 * @category   Mage
 * @package    Mage_Adminhtml
 * @author     Dmitriy Soroka <dmitriy.soroka@varien.com>
 */
class Mage_Adminhtml_Block_Sales_Order_Totals extends Mage_Adminhtml_Block_Template
{
    /**
     * Initialize template
     */
    protected function _construct()
    {
        parent::_construct();
        $this->setTemplate('sales/order/totals.phtml');
    }

    /**
     * Retrieve data source instance
     *
     * @return Mage_Sales_Model_Order
     */
    public function getSource()
    {
        return $this->getData('source');
    }

    /**
     * Retrieve currency instance
     *
     * @return Mage_Directory_Model_Currency
     */
    public function getCurrency()
    {
        return $this->getData('currency');
    }
}
