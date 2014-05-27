<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Test positions of the Reward total collectors as compared to other collectors
 */
namespace Magento\Reward\Model;

class CollectorPositionsTest extends \Magento\Sales\Model\AbstractCollectorPositionsTest
{
    /**
     * @return array
     */
    public function collectorPositionDataProvider()
    {
        return array(
            'quote collectors' => array(
                'reward',
                'quote',
                array(),
                array('weee', 'discount', 'tax', 'tax_subtotal', 'grand_total', 'giftcardaccount', 'customerbalance')
            ),
            'invoice collectors' => array(
                'reward',
                'invoice',
                array('giftcardaccount', 'customerbalance'),
                array('grand_total')
            ),
            'creditmemo collectors' => array(
                'reward',
                'creditmemo',
                array(),
                array('weee', 'discount', 'tax', 'grand_total', 'customerbalance', 'giftcardaccount')
            )
        );
    }
}
