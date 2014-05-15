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
    public function __construct(array $defaultConfig = [], array $defaultData = [])
    {
        $this->_data['gift_card_account'] = [
            'status' => 'Yes',
            'is_redeemable' => 'Yes',
            'website_id' => 'Main Website',
            'balance' => '100',
            'date_expires' => '01/01/2054',
            'recipient_email' => 'johndoe@example.com',
            'recipient_name' => 'John Doe',
            'recipient_store' => 'Default Store View',
        ];
    }
}
