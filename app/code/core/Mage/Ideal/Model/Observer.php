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
 * @package    Mage_Ideal
 * @copyright  Copyright (c) 2008 Irubin Consulting Inc. DBA Varien (http://www.varien.com)
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * iDEAL module observer
 *
 * @category    Mage
 * @package     Mage_Ideal
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Mage_Ideal_Model_Observer
{
    /**
     * Convert specific attributes from Quote Payment to Order Payment
     *
     * @param Varien_Object $observer
     * @return Mage_Ideal_Model_Observer
     */
    public function convertPayment($observer)
    {
        $orderPayment = $observer->getEvent()->getOrderPayment();
        $quotePayment = $observer->getEvent()->getQuotePayment();
        $orderPayment->setIdealIssuerId($quotePayment->getIdealIssuerId());

        if ($quotePayment->getIdealIssuerId()) {
            $issuerList = unserialize($quotePayment->getIdealIssuerList());
            if (isset($issuerList[$quotePayment->getIdealIssuerId()])) {
                $orderPayment->setIdealIssuerTitle(
                    $issuerList[$quotePayment->getIdealIssuerId()]
                );
            }
        }
        return $this;
    }
}
