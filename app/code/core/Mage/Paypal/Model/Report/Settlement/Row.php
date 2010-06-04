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
        /**
         * for translations
         */
        // Mage::helper('paypal')->__('General: received payment of a type not belonging to the other T00xx categories')
        // Mage::helper('paypal')->__('Mass Pay Payment')
        // Mage::helper('paypal')->__('Subscription Payment, either payment sent or payment received')
        // Mage::helper('paypal')->__('Preapproved Payment (BillUser API), either sent or received')
        // Mage::helper('paypal')->__('eBay Auction Payment')
        // Mage::helper('paypal')->__('Direct Payment API')
        // Mage::helper('paypal')->__('Express Checkout APIs')
        // Mage::helper('paypal')->__('Website Payments Standard Payment')
        // Mage::helper('paypal')->__('Postage Payment to either USPS or UPS')
        // Mage::helper('paypal')->__('Gift Certificate Payment: purchase of Gift Certificate')
        // Mage::helper('paypal')->__('Auction Payment other than through eBay')
        // Mage::helper('paypal')->__('Mobile Payment (made via a mobile phone)')
        // Mage::helper('paypal')->__('Virtual Terminal Payment')
        // Mage::helper('paypal')->__('General: non-payment fee of a type not belonging to the other T01xx categories')
        // Mage::helper('paypal')->__('Fee: Web Site Payments Pro Account Monthly')
        // Mage::helper('paypal')->__('Fee: Foreign ACH Withdrawal')
        // Mage::helper('paypal')->__('Fee: WorldLink Check Withdrawal')
        // Mage::helper('paypal')->__('Fee: Mass Pay Request')
        // Mage::helper('paypal')->__('General Currency Conversion')
        // Mage::helper('paypal')->__('User-initiated Currency Conversion')
        // Mage::helper('paypal')->__('Currency Conversion required to cover negative balance')
        // Mage::helper('paypal')->__('General Funding of PayPal Account ')
        // Mage::helper('paypal')->__('PayPal Balance Manager function of PayPal account')
        // Mage::helper('paypal')->__('ACH Funding for Funds Recovery from Account Balance')
        // Mage::helper('paypal')->__('EFT Funding (German banking)')
        // Mage::helper('paypal')->__('General Withdrawal from PayPal Account')
        // Mage::helper('paypal')->__('AutoSweep')
        // Mage::helper('paypal')->__('General: Use of PayPal account for purchasing as well as receiving payments')
        // Mage::helper('paypal')->__('Virtual PayPal Debit Card Transaction')
        // Mage::helper('paypal')->__('PayPal Debit Card Withdrawal from ATM')
        // Mage::helper('paypal')->__('Hidden Virtual PayPal Debit Card Transaction')
        // Mage::helper('paypal')->__('PayPal Debit Card Cash Advance')
        // Mage::helper('paypal')->__('General: Withdrawal from PayPal Account')
        // Mage::helper('paypal')->__('General (Purchase with a credit card)')
        // Mage::helper('paypal')->__('Negative Balance')
        // Mage::helper('paypal')->__('General: bonus of a type not belonging to the other T08xx categories')
        // Mage::helper('paypal')->__('Debit Card Cash Back')
        // Mage::helper('paypal')->__('Merchant Referral Bonus')
        // Mage::helper('paypal')->__('Balance Manager Account Bonus')
        // Mage::helper('paypal')->__('PayPal Buyer Warranty Bonus')
        // Mage::helper('paypal')->__('PayPal Protection Bonus')
        // Mage::helper('paypal')->__('Bonus for first ACH Use')
        // Mage::helper('paypal')->__('General Redemption')
        // Mage::helper('paypal')->__('Gift Certificate Redemption')
        // Mage::helper('paypal')->__('Points Incentive Redemption')
        // Mage::helper('paypal')->__('Coupon Redemption')
        // Mage::helper('paypal')->__('Reward Voucher Redemption')
        // Mage::helper('paypal')->__('General. Product no longer supported')
        // Mage::helper('paypal')->__('General: reversal of a type not belonging to the other T11xx categories')
        // Mage::helper('paypal')->__('ACH Withdrawal')
        // Mage::helper('paypal')->__('Debit Card Transaction')
        // Mage::helper('paypal')->__('Reversal of Points Usage')
        // Mage::helper('paypal')->__('ACH Deposit (Reversal)')
        // Mage::helper('paypal')->__('Reversal of General Account Hold')
        // Mage::helper('paypal')->__('Account-to-Account Payment, initiated by PayPal')
        // Mage::helper('paypal')->__('Payment Refund initiated by merchant')
        // Mage::helper('paypal')->__('Fee Reversal')
        // Mage::helper('paypal')->__('Hold for Dispute Investigation')
        // Mage::helper('paypal')->__('Reversal of hold for Dispute Investigation')
        // Mage::helper('paypal')->__('General: adjustment of a type not belonging to the other T12xx categories')
        // Mage::helper('paypal')->__('Chargeback')
        // Mage::helper('paypal')->__('Reversal')
        // Mage::helper('paypal')->__('Charge-off')
        // Mage::helper('paypal')->__('Incentive')
        // Mage::helper('paypal')->__('Reimbursement of Chargeback')
        // Mage::helper('paypal')->__('General (Authorization)')
        // Mage::helper('paypal')->__('Reauthorization')
        // Mage::helper('paypal')->__('Void')
        // Mage::helper('paypal')->__('General (Dividend)')
        // Mage::helper('paypal')->__('General: temporary hold of a type not belonging to the other T15xx categories')
        // Mage::helper('paypal')->__('Open Authorization')
        // Mage::helper('paypal')->__('ACH Deposit (Hold for Dispute or Other Investigation)')
        // Mage::helper('paypal')->__('Available Balance')
        // Mage::helper('paypal')->__('Funding')
        // Mage::helper('paypal')->__('General: Withdrawal to Non-Bank Entity')
        // Mage::helper('paypal')->__('WorldLink Withdrawal')
        // Mage::helper('paypal')->__('Buyer Credit Payment')
        // Mage::helper('paypal')->__('General Adjustment without businessrelated event')
        // Mage::helper('paypal')->__('General (Funds Transfer from PayPal Account to Another)')
        // Mage::helper('paypal')->__('Settlement Consolidation')
        // Mage::helper('paypal')->__('General: event not yet categorized')

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
            foreach ($events as $key => $event) {
                $events[$key] = Mage::helper('paypal')->__($event);
            }
            asort($events);
            return $events;
        }
        if (isset($events[$code])) {
            return Mage::helper('paypal')->__($events[$code]);
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
