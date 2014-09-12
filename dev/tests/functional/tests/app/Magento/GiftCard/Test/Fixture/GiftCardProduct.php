<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\GiftCard\Test\Fixture;

use Mtf\System\Config;
use Mtf\Handler\HandlerFactory;
use Mtf\Fixture\FixtureFactory;
use Mtf\Repository\RepositoryFactory;
use Mtf\System\Event\EventManagerInterface;
use Magento\Catalog\Test\Fixture\CatalogProductSimple;

/**
 * Class GiftCardProduct
 * Fixture for GiftCard product
 *
 * @SuppressWarnings(PHPMD.TooManyFields)
 * @SuppressWarnings(PHPMD.ExcessivePublicCount)
 */
class GiftCardProduct extends CatalogProductSimple
{
    /**
     * @var string
     */
    protected $repositoryClass = 'Magento\GiftCard\Test\Repository\GiftCardProduct';

    /**
     * @var string
     */
    protected $handlerInterface = 'Magento\GiftCard\Test\Handler\GiftCardProduct\GiftCardProductInterface';

    /**
     * Constructor
     *
     * @constructor
     * @param Config $configuration
     * @param RepositoryFactory $repositoryFactory
     * @param FixtureFactory $fixtureFactory
     * @param HandlerFactory $handlerFactory
     * @param EventManagerInterface $eventManager
     * @param array $data
     * @param string $dataSet
     * @param bool $persist
     */
    public function __construct(
        Config $configuration,
        RepositoryFactory $repositoryFactory,
        FixtureFactory $fixtureFactory,
        HandlerFactory $handlerFactory,
        EventManagerInterface $eventManager,
        array $data = [],
        $dataSet = '',
        $persist = false
    ) {
        if (!isset($data['url_key']) && isset($data['name'])) {
            $data['url_key'] = trim(strtolower(preg_replace('#[^0-9a-z%]+#i', '-', $data['name'])), '-');
        }
        parent::__construct(
            $configuration,
            $repositoryFactory,
            $fixtureFactory,
            $handlerFactory,
            $eventManager,
            $data,
            $dataSet,
            $persist
        );
    }

    /**
     * @var array
     */
    protected $defaultDataSet = [
        'name' => 'Test product giftcard %isolation%',
        'sku' => 'sku_test_product_giftcard_%isolation%',
        'giftcard_type' => 'Virtual',
        'giftcard_amounts' => [
            1 => ['price' => 120,],
            2 => ['price' => 150,]
        ],
        'quantity_and_stock_status' => [
            'qty' => 333.0000,
            'is_in_stock' => 'In Stock',
        ],
        'attribute_set_id' => ['dataSet' => 'default']
    ];

    protected $dataConfig = [
        'create_url_params' => [
            'type' => 'giftcard',
            'set' => '4',
        ],
        'input_prefix' => 'product',
    ];

    protected $allow_message = [
        'attribute_code' => 'allow_message',
        'backend_type' => 'int',
        'is_required' => '0',
        'default_value' => '',
        'input' => 'text',
        'group' => 'giftcard',
    ];

    protected $allow_open_amount = [
        'attribute_code' => 'allow_open_amount',
        'backend_type' => 'int',
        'is_required' => '0',
        'default_value' => '',
        'input' => 'select',
        'group' => 'product-details',
    ];

    protected $email_template = [
        'attribute_code' => 'email_template',
        'backend_type' => 'varchar',
        'is_required' => '0',
        'default_value' => '',
        'input' => 'text',
        'group' => 'giftcard',
    ];

    protected $giftcard_amounts = [
        'attribute_code' => 'giftcard_amounts',
        'backend_type' => 'composite',
        'is_required' => '0',
        'default_value' => '',
        'input' => 'price',
        'group' => 'product-details',
    ];

    protected $giftcard_type = [
        'attribute_code' => 'giftcard_type',
        'backend_type' => 'int',
        'is_required' => '1',
        'default_value' => '',
        'input' => 'select',
        'group' => 'product-details',
    ];

    protected $gift_wrapping_available = [
        'attribute_code' => 'gift_wrapping_available',
        'backend_type' => 'varchar',
        'is_required' => '0',
        'default_value' => '',
        'input' => 'select',
    ];

    protected $gift_wrapping_price = [
        'attribute_code' => 'gift_wrapping_price',
        'backend_type' => 'decimal',
        'is_required' => '0',
        'default_value' => '',
        'input' => 'price',
        'group' => 'autosettings'
    ];

    protected $is_redeemable = [
        'attribute_code' => 'is_redeemable',
        'backend_type' => 'int',
        'is_required' => '0',
        'default_value' => '',
        'input' => 'text',
        'group' => 'giftcard',
    ];

    protected $lifetime = [
        'attribute_code' => 'lifetime',
        'backend_type' => 'int',
        'is_required' => '0',
        'default_value' => '',
        'input' => 'text',
        'group' => 'giftcard',
    ];

    protected $open_amount_max = [
        'attribute_code' => 'open_amount_max',
        'backend_type' => 'decimal',
        'is_required' => '0',
        'default_value' => '',
        'input' => 'price',
        'group' => 'product-details',
    ];

    protected $open_amount_min = [
        'attribute_code' => 'open_amount_min',
        'backend_type' => 'decimal',
        'is_required' => '0',
        'default_value' => '',
        'input' => 'price',
        'group' => 'product-details',
    ];

    protected $related_tgtr_position_behavior = [
        'attribute_code' => 'related_tgtr_position_behavior',
        'backend_type' => 'int',
        'is_required' => '0',
        'default_value' => '',
        'input' => 'text',
    ];

    protected $related_tgtr_position_limit = [
        'attribute_code' => 'related_tgtr_position_limit',
        'backend_type' => 'int',
        'is_required' => '0',
        'default_value' => '',
        'input' => 'text',
    ];

    protected $upsell_tgtr_position_behavior = [
        'attribute_code' => 'upsell_tgtr_position_behavior',
        'backend_type' => 'int',
        'is_required' => '0',
        'default_value' => '',
        'input' => 'text',
    ];

    protected $upsell_tgtr_position_limit = [
        'attribute_code' => 'upsell_tgtr_position_limit',
        'backend_type' => 'int',
        'is_required' => '0',
        'default_value' => '',
        'input' => 'text',
    ];

    protected $use_config_allow_message = [
        'attribute_code' => 'use_config_allow_message',
        'backend_type' => 'int',
        'is_required' => '0',
        'default_value' => '',
        'input' => 'text',
        'group' => 'giftcard',
    ];

    protected $use_config_email_template = [
        'attribute_code' => 'use_config_email_template',
        'backend_type' => 'int',
        'is_required' => '0',
        'default_value' => '',
        'input' => 'text',
        'group' => 'giftcard',
    ];

    protected $use_config_is_redeemable = [
        'attribute_code' => 'use_config_is_redeemable',
        'backend_type' => 'int',
        'is_required' => '0',
        'default_value' => '',
        'input' => 'text',
        'group' => 'giftcard',
    ];

    protected $use_config_lifetime = [
        'attribute_code' => 'use_config_lifetime',
        'backend_type' => 'int',
        'is_required' => '0',
        'default_value' => '',
        'input' => 'text',
        'group' => 'giftcard',
    ];

    protected $price = [
        'attribute_code' => 'price',
        'backend_type' => 'virtual',
        'source' => 'Magento\GiftCard\Test\Fixture\GiftCardProduct\Price',
    ];

    public function getAllowMessage()
    {
        return $this->getData('allow_message');
    }

    public function getAllowOpenAmount()
    {
        return $this->getData('allow_open_amount');
    }

    public function getEmailTemplate()
    {
        return $this->getData('email_template');
    }

    public function getGiftcardAmounts()
    {
        return $this->getData('giftcard_amounts');
    }

    public function getGiftcardType()
    {
        return $this->getData('giftcard_type');
    }

    public function getGiftWrappingAvailable()
    {
        return $this->getData('gift_wrapping_available');
    }

    public function getGiftWrappingPrice()
    {
        return $this->getData('gift_wrapping_price');
    }

    public function getIsRedeemable()
    {
        return $this->getData('is_redeemable');
    }

    public function getLifetime()
    {
        return $this->getData('lifetime');
    }

    public function getOpenAmountMax()
    {
        return $this->getData('open_amount_max');
    }

    public function getOpenAmountMin()
    {
        return $this->getData('open_amount_min');
    }

    public function getRelatedTgtrPositionBehavior()
    {
        return $this->getData('related_tgtr_position_behavior');
    }

    public function getRelatedTgtrPositionLimit()
    {
        return $this->getData('related_tgtr_position_limit');
    }

    public function getUpsellTgtrPositionBehavior()
    {
        return $this->getData('upsell_tgtr_position_behavior');
    }

    public function getUpsellTgtrPositionLimit()
    {
        return $this->getData('upsell_tgtr_position_limit');
    }

    public function getUseConfigAllowMessage()
    {
        return $this->getData('use_config_allow_message');
    }

    public function getUseConfigEmailTemplate()
    {
        return $this->getData('use_config_email_template');
    }

    public function getUseConfigIsRedeemable()
    {
        return $this->getData('use_config_is_redeemable');
    }

    public function getUseConfigLifetime()
    {
        return $this->getData('use_config_lifetime');
    }
}
