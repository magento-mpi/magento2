<?php
/**
 * {license_notice}
 *
 * @category    Mtf
 * @package     Mtf
 * @subpackage  functional_tests
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\GiftCard\Test\Fixture;

use Mtf\System\Config;
use Mtf\Factory\Factory;
use Magento\Catalog\Test\Fixture\AbstractProduct;

class GiftCard extends AbstractProduct
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

        $data = array(
            'giftcard_type' => array(
            'value' => 'Virtual',
            'input_value' => '0',
            'group' => static::GROUP_PRODUCT_DETAILS,
            'input' => 'select'
            ),
            'allow_open_amount' => array(
                'value' => 'Yes',
                'input_name' => 'product[allow_open_amount]',
                'input_value' => 'Yes',
                'group' => static::GROUP_PRODUCT_DETAILS,
                'input' => 'checkbox')
        );
        $this->_data['fields'] = array_merge($this->_data['fields'], $data);

        $this->_repository = Factory::getRepositoryFactory()
            ->getMagentoGiftCardGiftCard($this->_dataConfig, $this->_data);
    }

    /**
     * Create Gift Card
     *
     * @return $this|void
     */
    public function persist()
    {
        Factory::getApp()->magentoGiftCardCreateGiftCard($this);

        return $this;
    }
}
