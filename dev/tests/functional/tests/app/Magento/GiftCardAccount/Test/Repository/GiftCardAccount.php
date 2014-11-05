<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\GiftCardAccount\Test\Repository;

use Mtf\Repository\AbstractRepository;

/**
 * Class GiftCardAccount
 * Gift card account repository
 */
class GiftCardAccount extends AbstractRepository
{
    /**
     * @constructor
     * @param array $defaultConfig
     * @param array $defaultData
     *
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function __construct(array $defaultConfig = [], array $defaultData = [])
    {
        $this->_data['default'] = [
            'status' => 'Yes',
            'is_redeemable' => 'Yes',
            'website_id' => 'Main Website',
            'balance' => '100',
            'date_expires' => '01/01/2054',
            'recipient_email' => 'johndoe@example.com',
            'recipient_name' => 'John Doe',
            'recipient_store' => 'Default Store View',
        ];

        $this->_data['active_redeemable_account'] = [
            'status' => 'Yes',
            'is_redeemable' => 'Yes',
            'website_id' => 1,
            'balance' => '10',
        ];

        $this->_data['gift_card_account_amount_1'] = [
            'status' => 'Yes',
            'is_redeemable' => 'Yes',
            'website_id' => 1,
            'balance' => '1',
        ];
    }
}
