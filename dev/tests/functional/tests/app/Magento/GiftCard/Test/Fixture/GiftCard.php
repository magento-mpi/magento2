<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\GiftCard\Test\Fixture;

use Mtf\Factory\Factory;
use Magento\Catalog\Test\Fixture\Product;

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
        $this->_dataConfig = array(
            'constraint' => 'Success',
            'create_url_params' => array(
                'type' => 'giftcard',
                'set' => 4,
            ),
            'input_prefix' => 'product'
        );

        $this->_repository = Factory::getRepositoryFactory()
            ->getMagentoGiftCardGiftCard($this->_dataConfig, $this->_data);
    }
}
