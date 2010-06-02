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
 * @package     Mage_Paypal
 * @copyright   Copyright (c) 2010 Irubin Consulting Inc. DBA Varien (http://www.varien.com)
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/*
 * Model for report rows
 */
class Mage_Paypal_Model_Report_Settlement_Row extends Mage_Core_Model_Abstract
{
    /**
     * Initialize resource model
     */
	protected function _construct()
    {
        $this->_init('paypal/report_settlement_row');
    }

    /**
     * Return description of Reference ID Type
     * If no code specified, return full list of codes with their description
     *
     * @param string code
     * @return string|array
     */
    public function getReferenceType($code = null)
    {
        $types = array(
            'TXN' => Mage::helper('paypal')->__('Transaction ID'),
            'ODR' => Mage::helper('paypal')->__('Order ID'),
            'SUB' => Mage::helper('paypal')->__('Subscription ID'),
            'PAP' => Mage::helper('paypal')->__('Preapproved Payment ID')
        );
        if($code === null) {
            asort($types);
            return $types;
        }
        if (isset($types[$code])) {
            return $types[$code];
        }
        return $code;
    }

    /**
     * Return native description of transaction code
     * If no code specified, return full list of codes with their description
     *
     * @param string code
     * @return string|array
     */
    public function getTransactionEvent($code = null)
    {
        $events = array(
            'T0000' => 'General: received payment of a type not belonging to the other T00xx categories',
            'T0001' => 'Mass Pay Payment',
            'T0002' => 'Subscription Payment, either payment sent or payment received',
            'T0003' => 'Preapproved Payment (BillUser API), either sent or received',
            'T0004' => 'eBay Auction Payment',
            'T0005' => 'Direct Payment API',
            'T0006' => 'Express Checkout APIs',
            'T0007' => 'Website Payments Standard Payment',
            'T0008' => 'Postage Payment to either USPS or UPS',
            'T0009' => 'Gift Certificate Payment: purchase of Gift Certificate',
            'T0010' => 'Auction Payment other than through eBay',
            'T0011' => 'Mobile Payment (made via a mobile phone)',
            'T0012' => 'Virtual Terminal Payment',
            'T0100' => 'General: non-payment fee of a type not belonging to the other T01xx categories',
            'T0101' => 'Fee: Web Site Payments Pro Account Monthly',
            'T0102' => 'Fee: Foreign ACH Withdrawal',
            'T0103' => 'Fee: WorldLink Check Withdrawal',
            'T0104' => 'Fee: Mass Pay Request',
            'T0200' => 'General Currency Conversion',
            'T0201' => 'User-initiated Currency Conversion',
            'T0202' => 'Currency Conversion required to cover negative balance',
            'T0300' => 'General Funding of PayPal Account ',
            'T0301' => 'PayPal Balance Manager function of PayPal account',
            'T0302' => 'ACH Funding for Funds Recovery from Account Balance',
            'T0303' => 'EFT Funding (German banking)',
            'T0400' => 'General Withdrawal from PayPal Account',
            'T0401' => 'AutoSweep',
            'T0500' => 'General: Use of PayPal account for purchasing as well as receiving payments',
            'T0501' => 'Virtual PayPal Debit Card Transaction',
            'T0502' => 'PayPal Debit Card Withdrawal from ATM',
            'T0503' => 'Hidden Virtual PayPal Debit Card Transaction',
            'T0504' => 'PayPal Debit Card Cash Advance',
            'T0600' => 'General: Withdrawal from PayPal Account',
            'T0700' => 'General (Purchase with a credit card)',
            'T0701' => 'Negative Balance',
            'T0800' => 'General: bonus of a type not belonging to the other T08xx categories',
            'T0801' => 'Debit Card Cash Back',
            'T0802' => 'Merchant Referral Bonus',
            'T0803' => 'Balance Manager Account Bonus',
            'T0804' => 'PayPal Buyer Warranty Bonus',
            'T0805' => 'PayPal Protection Bonus',
            'T0806' => 'Bonus for first ACH Use',
            'T0900' => 'General Redemption',
            'T0901' => 'Gift Certificate Redemption',
            'T0902' => 'Points Incentive Redemption',
            'T0903' => 'Coupon Redemption',
            'T0904' => 'Reward Voucher Redemption',
            'T1000' => 'General. Product no longer supported',
            'T1100' => 'General: reversal of a type not belonging to the other T11xx categories',
            'T1101' => 'ACH Withdrawal',
            'T1102' => 'Debit Card Transaction',
            'T1103' => 'Reversal of Points Usage',
            'T1104' => 'ACH Deposit (Reversal)',
            'T1105' => 'Reversal of General Account Hold',
            'T1106' => 'Account-to-Account Payment, initiated by PayPal',
            'T1107' => 'Payment Refund initiated by merchant',
            'T1108' => 'Fee Reversal',
            'T1110' => 'Hold for Dispute Investigation',
            'T1111' => 'Reversal of hold for Dispute Investigation',
            'T1200' => 'General: adjustment of a type not belonging to the other T12xx categories',
            'T1201' => 'Chargeback',
            'T1202' => 'Reversal',
            'T1203' => 'Charge-off',
            'T1204' => 'Incentive',
            'T1205' => 'Reimbursement of Chargeback',
            'T1300' => 'General (Authorization)',
            'T1301' => 'Reauthorization',
            'T1302' => 'Void',
            'T1400' => 'General (Dividend)',
            'T1500' => 'General: temporary hold of a type not belonging to the other T15xx categories',
            'T1501' => 'Open Authorization',
            'T1502' => 'ACH Deposit (Hold for Dispute or Other Investigation)',
            'T1503' => 'Available Balance',
            'T1600' => 'Funding',
            'T1700' => 'General: Withdrawal to Non-Bank Entity',
            'T1701' => 'WorldLink Withdrawal',
            'T1800' => 'Buyer Credit Payment',
            'T1900' => 'General Adjustment without businessrelated event',
            'T2000' => 'General (Funds Transfer from PayPal Account to Another)',
            'T2001' => 'Settlement Consolidation',
            'T9900' => 'General: event not yet categorized',
        );
        if($code === null) {
            asort($events);
            return $events;
        }
        if (isset($events[$code])) {
            return $events[$code];
        }
        return $code;
    }

    /**
     * Return description of "Debit or Credit" value
     * If no code specified, return full list of codes with their description
     *
     * @param string code
     * @return string|array
     */
    public function getDebitCreditText($code = null)
    {
        $options = array(
            'CR' => Mage::helper('paypal')->__('Credit'),
            'DR' => Mage::helper('paypal')->__('Debit'),
        );
        if($code === null) {
            return $options;
        }
        if (isset($options[$code])) {
            return $options[$code];
        }
        return $code;
    }
}
