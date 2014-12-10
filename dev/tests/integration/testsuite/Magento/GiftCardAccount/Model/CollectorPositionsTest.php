<?php
/**
 * @copyright Copyright (c) 2014 X.commerce, Inc. (http://www.magentocommerce.com)
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
        return [
            'quote collectors' => [
                'giftcardaccount',
                'quote',
                ['customerbalance'],
                ['weee', 'discount', 'tax', 'tax_subtotal', 'grand_total'],
            ],
            'invoice collectors' => [
                'giftcardaccount',
                'invoice',
                ['customerbalance'],
                ['discount', 'tax', 'grand_total'],
            ],
            'creditmemo collectors' => [
                'giftcardaccount',
                'creditmemo',
                [],
                ['weee', 'discount', 'tax', 'grand_total', 'customerbalance'],
            ]
        ];
    }
}
