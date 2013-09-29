<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Test positions of the GiftCardAccount total collectors as compared to other collectors
 */
namespace Magento\GiftCardAccount\Model;

class CollectorPositionsTest extends \Magento\Sales\Model\AbstractCollectorPositionsTest
{
    /**
     * @return array
     */
    public function collectorPositionDataProvider()
    {
        return array(
            'quote collectors' => array(
                'giftcardaccount',
                'quote',
                array('customerbalance'),
                array('weee', 'discount', 'tax', 'tax_subtotal', 'grand_total'),
            ),
            'invoice collectors' => array(
                'giftcardaccount',
                'invoice',
                array('customerbalance'),
                array('discount', 'tax', 'grand_total'),
            ),
            'creditmemo collectors' => array(
                'giftcardaccount',
                'creditmemo',
                array(),
                array('weee', 'discount', 'tax', 'grand_total', 'customerbalance'),
            ),
        );
    }
}
