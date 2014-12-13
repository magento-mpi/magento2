<?php
/**
 * @copyright Copyright (c) 2014 X.commerce, Inc. (http://www.magentocommerce.com)
 */

/**
 * Test positions of the GiftWrapping total collectors as compared to other collectors
 */
namespace Magento\GiftWrapping\Model;

class CollectorPositionsTest extends \Magento\Sales\Model\AbstractCollectorPositionsTest
{
    /**
     * @return array
     */
    public function collectorPositionDataProvider()
    {
        return [
            'quote collectors' => ['giftwrapping', 'quote', [], ['subtotal']],
            'invoice collectors' => ['giftwrapping', 'invoice', ['giftcardaccount'], ['cost_total']],
            'creditmemo collectors' => [
                'giftwrapping',
                'creditmemo',
                ['giftcardaccount'],
                ['cost_total'],
            ],
            'tax quote collectors' => ['tax_giftwrapping', 'quote', ['grand_total'], ['tax']],
            'tax invoice collectors' => ['tax_giftwrapping', 'quote', ['grand_total'], ['tax']],
            'tax creditmemo collectors' => ['tax_giftwrapping', 'creditmemo', ['grand_total'], ['tax']]
        ];
    }
}
