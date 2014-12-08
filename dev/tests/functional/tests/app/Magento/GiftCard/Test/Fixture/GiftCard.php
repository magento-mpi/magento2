<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\GiftCard\Test\Fixture;

use Magento\Catalog\Test\Fixture\Product;
use Mtf\Factory\Factory;

/**
 * Class GiftCard
 *
 */
class GiftCard extends Product
{
    /**
     * Initialize fixture data
     */
    protected function _initData()
    {
        parent::_initData();
        $this->_dataConfig = [
            'constraint' => 'Success',
            'create_url_params' => [
                'type' => 'giftcard',
                'set' => 4,
            ],
            'input_prefix' => 'product',
        ];

        $this->_repository = Factory::getRepositoryFactory()
            ->getMagentoGiftCardGiftCard($this->_dataConfig, $this->_data);
    }
}
