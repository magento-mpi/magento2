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
 * @package    Mage_Paybox
 * @copyright  Copyright (c) 2008 Irubin Consulting Inc. DBA Varien (http://www.varien.com)
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * Paybox Adminhtml Cart Type select
 *
 * @category   Mage
 * @package    Mage_Paybox
 * @author     Magento Core Team <core@magentocommerce.com>
 */
class Mage_Paybox_Block_Adminhtml_Cart_Type_Select extends Mage_Adminhtml_Block_Template
{
    /**
     * Enter description here...
     *
     * @return Mage_Paybox_Model_System
     */
    public function getModel()
    {
        return Mage::getModel('paybox/system');
    }

    public function getParentHtmlId()
    {
        return substr($this->getDependHtmlId(), 0, strrpos($this->getDependHtmlId(), 'typecarte')) . 'typepaiement';
    }

    public function getJsonCartTypes()
    {
        return $this->getModel()->getJsonCartTypes();
    }

}