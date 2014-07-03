<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\GiftCard\Test\Fixture;

use Mtf\Fixture\InjectableFixture;

/**
 * Class GiftCardProduct
 */
class GiftCardProduct extends InjectableFixture
{
    /**
     * @var string
     */
    protected $repositoryClass = 'Magento\GiftCard\Test\Repository\GiftCardProduct';

    /**
     * @var string
     */
    protected $handlerInterface = 'Magento\GiftCard\Test\Handler\GiftCardProduct\GiftCardProductInterface';

    protected $dataConfig = [
        'create_url_params' => [
            'type' => 'giftcard',
            'set' => '4',
        ],
        'input_prefix' => 'product',
    ];

    protected $defaultDataSet = [
        'is_returnable' => null,
        'options_container' => null,
        'quantity_and_stock_status' => null,
        'status' => null,
        'visibility' => null,
    ];


    protected $website_ids = [
        'attribute_code' => 'website_ids',
        'backend_type' => 'virtual',
        'default_value' => 'Main Website',
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

    protected $category_ids = [
        'attribute_code' => 'category_ids',
        'backend_type' => 'static',
        'is_required' => '0',
        'default_value' => '',
        'input' => 'text',
        'source' => 'Magento\Catalog\Test\Fixture\CatalogProductSimple\CategoryIds',
    ];

    protected $created_at = [
        'attribute_code' => 'created_at',
        'backend_type' => 'static',
        'is_required' => '1',
        'default_value' => '',
        'input' => 'text',
    ];

    protected $custom_design = [
        'attribute_code' => 'custom_design',
        'backend_type' => 'varchar',
        'is_required' => '0',
        'default_value' => '',
        'input' => 'select',
    ];

    protected $custom_design_from = [
        'attribute_code' => 'custom_design_from',
        'backend_type' => 'datetime',
        'is_required' => '0',
        'default_value' => '',
        'input' => 'date',
    ];

    protected $custom_design_to = [
        'attribute_code' => 'custom_design_to',
        'backend_type' => 'datetime',
        'is_required' => '0',
        'default_value' => '',
        'input' => 'date',
    ];

    protected $custom_layout_update = [
        'attribute_code' => 'custom_layout_update',
        'backend_type' => 'text',
        'is_required' => '0',
        'default_value' => '',
        'input' => 'textarea',
    ];

    protected $description = [
        'attribute_code' => 'description',
        'backend_type' => 'text',
        'is_required' => '0',
        'default_value' => '',
        'input' => 'textarea',
    ];

    protected $group = [
    ];

    protected $email_template = [
        'attribute_code' => 'email_template',
        'backend_type' => 'varchar',
        'is_required' => '0',
        'default_value' => '',
        'input' => 'text',
        'group' => 'giftcard',
    ];

    protected $gallery = [
        'attribute_code' => 'gallery',
        'backend_type' => 'varchar',
        'is_required' => '0',
        'default_value' => '',
        'input' => 'gallery',
    ];

    protected $giftcard_amounts = [
        'attribute_code' => 'giftcard_amounts',
        'backend_type' => 'decimal',
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

    protected $gift_message_available = [
        'attribute_code' => 'gift_message_available',
        'backend_type' => 'varchar',
        'is_required' => '0',
        'default_value' => '',
        'input' => 'select',
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
    ];

    protected $has_options = [
        'attribute_code' => 'has_options',
        'backend_type' => 'static',
        'is_required' => '0',
        'default_value' => '',
        'input' => 'text',
    ];

    protected $image = [
        'attribute_code' => 'image',
        'backend_type' => 'varchar',
        'is_required' => '0',
        'default_value' => '',
        'input' => 'media_image',
    ];

    protected $image_label = [
        'attribute_code' => 'image_label',
        'backend_type' => 'varchar',
        'is_required' => '0',
        'default_value' => '',
        'input' => 'text',
    ];

    protected $is_redeemable = [
        'attribute_code' => 'is_redeemable',
        'backend_type' => 'int',
        'is_required' => '0',
        'default_value' => '',
        'input' => 'text',
        'group' => 'giftcard',
    ];

    protected $is_returnable = [
        'attribute_code' => 'is_returnable',
        'backend_type' => 'varchar',
        'is_required' => '0',
        'default_value' => '2',
        'input' => 'select',
    ];

    protected $lifetime = [
        'attribute_code' => 'lifetime',
        'backend_type' => 'int',
        'is_required' => '0',
        'default_value' => '',
        'input' => 'text',
        'group' => 'giftcard',
    ];

    protected $media_gallery = [
        'attribute_code' => 'media_gallery',
        'backend_type' => 'varchar',
        'is_required' => '0',
        'default_value' => '',
        'input' => 'gallery',
    ];

    protected $meta_description = [
        'attribute_code' => 'meta_description',
        'backend_type' => 'varchar',
        'is_required' => '0',
        'default_value' => '',
        'input' => 'textarea',
    ];

    protected $meta_keyword = [
        'attribute_code' => 'meta_keyword',
        'backend_type' => 'text',
        'is_required' => '0',
        'default_value' => '',
        'input' => 'textarea',
    ];

    protected $meta_title = [
        'attribute_code' => 'meta_title',
        'backend_type' => 'varchar',
        'is_required' => '0',
        'default_value' => '',
        'input' => 'text',
    ];

    protected $name = [
        'attribute_code' => 'name',
        'backend_type' => 'varchar',
        'is_required' => '1',
        'default_value' => '',
        'input' => 'text',
    ];

    protected $news_from_date = [
        'attribute_code' => 'news_from_date',
        'backend_type' => 'datetime',
        'is_required' => '0',
        'default_value' => '',
        'input' => 'date',
        'source' => 'Magento\Backend\Test\Fixture\Date',
    ];

    protected $news_to_date = [
        'attribute_code' => 'news_to_date',
        'backend_type' => 'datetime',
        'is_required' => '0',
        'default_value' => '',
        'input' => 'date',
        'source' => 'Magento\Backend\Test\Fixture\Date',
    ];

    protected $old_id = [
        'attribute_code' => 'old_id',
        'backend_type' => 'int',
        'is_required' => '0',
        'default_value' => '',
        'input' => 'text',
    ];

    protected $open_amount_max = [
        'attribute_code' => 'open_amount_max',
        'backend_type' => 'decimal',
        'is_required' => '0',
        'default_value' => '',
        'input' => 'price',
    ];

    protected $open_amount_min = [
        'attribute_code' => 'open_amount_min',
        'backend_type' => 'decimal',
        'is_required' => '0',
        'default_value' => '',
        'input' => 'price',
    ];

    protected $options_container = [
        'attribute_code' => 'options_container',
        'backend_type' => 'varchar',
        'is_required' => '0',
        'default_value' => 'container2',
        'input' => 'select',
    ];

    protected $page_layout = [
        'attribute_code' => 'page_layout',
        'backend_type' => 'varchar',
        'is_required' => '0',
        'default_value' => '',
        'input' => 'select',
    ];

    protected $quantity_and_stock_status = [
        'attribute_code' => 'quantity_and_stock_status',
        'backend_type' => 'int',
        'is_required' => '0',
        'default_value' => '1',
        'input' => 'select',
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

    protected $required_options = [
        'attribute_code' => 'required_options',
        'backend_type' => 'static',
        'is_required' => '0',
        'default_value' => '',
        'input' => 'text',
    ];

    protected $short_description = [
        'attribute_code' => 'short_description',
        'backend_type' => 'text',
        'is_required' => '0',
        'default_value' => '',
        'input' => 'textarea',
    ];

    protected $sku = [
        'attribute_code' => 'sku',
        'backend_type' => 'static',
        'is_required' => '1',
        'default_value' => '',
        'input' => 'text',
    ];

    protected $small_image = [
        'attribute_code' => 'small_image',
        'backend_type' => 'varchar',
        'is_required' => '0',
        'default_value' => '',
        'input' => 'media_image',
    ];

    protected $small_image_label = [
        'attribute_code' => 'small_image_label',
        'backend_type' => 'varchar',
        'is_required' => '0',
        'default_value' => '',
        'input' => 'text',
    ];

    protected $status = [
        'attribute_code' => 'status',
        'backend_type' => 'int',
        'is_required' => '0',
        'default_value' => '1',
        'input' => 'select',
        'group' => 'product-details',
    ];

    protected $thumbnail = [
        'attribute_code' => 'thumbnail',
        'backend_type' => 'varchar',
        'is_required' => '0',
        'default_value' => '',
        'input' => 'media_image',
    ];

    protected $thumbnail_label = [
        'attribute_code' => 'thumbnail_label',
        'backend_type' => 'varchar',
        'is_required' => '0',
        'default_value' => '',
        'input' => 'text',
    ];

    protected $updated_at = [
        'attribute_code' => 'updated_at',
        'backend_type' => 'static',
        'is_required' => '1',
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

    protected $url_key = [
        'attribute_code' => 'url_key',
        'backend_type' => 'varchar',
        'is_required' => '0',
        'default_value' => '',
        'input' => 'text',
    ];

    protected $url_path = [
        'attribute_code' => 'url_path',
        'backend_type' => 'varchar',
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

    protected $visibility = [
        'attribute_code' => 'visibility',
        'backend_type' => 'int',
        'is_required' => '0',
        'default_value' => '4',
        'input' => 'select',
    ];

    protected $weight = [
        'attribute_code' => 'weight',
        'backend_type' => 'decimal',
        'is_required' => '0',
        'default_value' => '',
        'input' => 'weight',
    ];

    protected $id = [
        'attribute_code' => 'id',
        'backend_type' => 'virtual',
    ];

    protected $custom_options = [
        'attribute_code' => 'custom_options',
        'backend_type' => 'virtual',
        'group' => 'product_info_tabs_customer_options',
        'is_required' => '0',
        'source' => 'Magento\Catalog\Test\Fixture\CatalogProductSimple\CustomOptions',
    ];

    public function getAllowMessage()
    {
        return $this->getData('allow_message');
    }

    public function getAllowOpenAmount()
    {
        return $this->getData('allow_open_amount');
    }

    public function getCategoryIds()
    {
        return $this->getData('category_ids');
    }

    public function getCreatedAt()
    {
        return $this->getData('created_at');
    }

    public function getCustomDesign()
    {
        return $this->getData('custom_design');
    }

    public function getCustomDesignFrom()
    {
        return $this->getData('custom_design_from');
    }

    public function getCustomDesignTo()
    {
        return $this->getData('custom_design_to');
    }

    public function getCustomLayoutUpdate()
    {
        return $this->getData('custom_layout_update');
    }

    public function getDescription()
    {
        return $this->getData('description');
    }

    public function getGroup()
    {
        return $this->getData('group');
    }

    public function getEmailTemplate()
    {
        return $this->getData('email_template');
    }

    public function getGallery()
    {
        return $this->getData('gallery');
    }

    public function getGiftcardAmounts()
    {
        return $this->getData('giftcard_amounts');
    }

    public function getGiftcardType()
    {
        return $this->getData('giftcard_type');
    }

    public function getGiftMessageAvailable()
    {
        return $this->getData('gift_message_available');
    }

    public function getGiftWrappingAvailable()
    {
        return $this->getData('gift_wrapping_available');
    }

    public function getGiftWrappingPrice()
    {
        return $this->getData('gift_wrapping_price');
    }

    public function getHasOptions()
    {
        return $this->getData('has_options');
    }

    public function getImage()
    {
        return $this->getData('image');
    }

    public function getImageLabel()
    {
        return $this->getData('image_label');
    }

    public function getIsRedeemable()
    {
        return $this->getData('is_redeemable');
    }

    public function getIsReturnable()
    {
        return $this->getData('is_returnable');
    }

    public function getLifetime()
    {
        return $this->getData('lifetime');
    }

    public function getMediaGallery()
    {
        return $this->getData('media_gallery');
    }

    public function getMetaDescription()
    {
        return $this->getData('meta_description');
    }

    public function getMetaKeyword()
    {
        return $this->getData('meta_keyword');
    }

    public function getMetaTitle()
    {
        return $this->getData('meta_title');
    }

    public function getName()
    {
        return $this->getData('name');
    }

    public function getNewsFromDate()
    {
        return $this->getData('news_from_date');
    }

    public function getNewsToDate()
    {
        return $this->getData('news_to_date');
    }

    public function getOldId()
    {
        return $this->getData('old_id');
    }

    public function getOpenAmountMax()
    {
        return $this->getData('open_amount_max');
    }

    public function getOpenAmountMin()
    {
        return $this->getData('open_amount_min');
    }

    public function getOptionsContainer()
    {
        return $this->getData('options_container');
    }

    public function getPageLayout()
    {
        return $this->getData('page_layout');
    }

    public function getQuantityAndStockStatus()
    {
        return $this->getData('quantity_and_stock_status');
    }

    public function getRelatedTgtrPositionBehavior()
    {
        return $this->getData('related_tgtr_position_behavior');
    }

    public function getRelatedTgtrPositionLimit()
    {
        return $this->getData('related_tgtr_position_limit');
    }

    public function getRequiredOptions()
    {
        return $this->getData('required_options');
    }

    public function getShortDescription()
    {
        return $this->getData('short_description');
    }

    public function getSku()
    {
        return $this->getData('sku');
    }

    public function getSmallImage()
    {
        return $this->getData('small_image');
    }

    public function getSmallImageLabel()
    {
        return $this->getData('small_image_label');
    }

    public function getStatus()
    {
        return $this->getData('status');
    }

    public function getThumbnail()
    {
        return $this->getData('thumbnail');
    }

    public function getThumbnailLabel()
    {
        return $this->getData('thumbnail_label');
    }

    public function getUpdatedAt()
    {
        return $this->getData('updated_at');
    }

    public function getUpsellTgtrPositionBehavior()
    {
        return $this->getData('upsell_tgtr_position_behavior');
    }

    public function getUpsellTgtrPositionLimit()
    {
        return $this->getData('upsell_tgtr_position_limit');
    }

    public function getUrlKey()
    {
        return $this->getData('url_key');
    }

    public function getUrlPath()
    {
        return $this->getData('url_path');
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

    public function getVisibility()
    {
        return $this->getData('visibility');
    }

    public function getWeight()
    {
        return $this->getData('weight');
    }

    public function getId()
    {
        return $this->getData('id');
    }

    public function getCustomOptions()
    {
        return $this->getData('custom_options');
    }
}
