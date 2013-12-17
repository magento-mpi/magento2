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

namespace Magento\GiftCard\Test\Repository;

use Magento\Catalog\Test\Fixture;
use Magento\Catalog\Test\Repository;

/**
 * Class GiftCard Repository
 *
 * @package Magento\GiftCard\Test\Repository
 */
class GiftCard extends Repository\Product
{
    /**
     * {@inheritdoc}
     */
    public function __construct(array $defaultConfig, array $defaultData)
    {
        parent::__construct($defaultConfig, $defaultData);
        $this->_data['virtual_open_amount'] = $this->getVirtualWithOpenAmount();
    }

    /**
     * Get virtual Gift Card with 'Allow Open Amount'
     *
     * @return array
     */
    protected function getVirtualWithOpenAmount()
    {
        $data = array(
            'data' => array(
                'fields' => array(
                    'giftcard_type' => array(
                        'value' => 'Virtual',
                        'input_value' => '0',
                        'group' => Fixture\Product::GROUP_PRODUCT_DETAILS,
                        'input' => 'select'
                    ),
                    'allow_open_amount' => array(
                        'value' => 'Yes',
                        'input_name' => 'product[allow_open_amount]',
                        'input_value' => 'Yes',
                        'group' => Fixture\Product::GROUP_PRODUCT_DETAILS,
                        'input' => 'checkbox'
                    )
                )
            )
        );

        return array_replace_recursive($this->_data['giftcard'], $data);
    }
}
