<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Downloadable\Test\Fixture;

use Mtf\Factory\Factory;
use Magento\Catalog\Test\Fixture\Product;

/**
 * Class DownloadableProduct
 * Fixture for Downloadable product
 */
class DownloadableProduct extends Product
{
    const GROUP = 'downloadable_information';

    const LINK_IS_SHAREABLE_NO_VALUE = 0;
    const LINK_IS_SHAREABLE_YES_VALUE = 1;
    const LINK_IS_SHAREABLE_USE_CONFIG_VALUE = 2;

    protected $defaultDataSet = [
        'name' => 'DownloadableProduct_%isolation%',
        'sku' => 'DownloadableProduct_%isolation%',
        'price' => '100',
        'tax_class' => 'Taxable Goods',
        'description' => 'This is description for downloadable product',
        'short_description' => 'This is short description for downloadable product',
        'quantity_and_stock_status_qty' => '1',
        'quantity_and_stock_status' => 'In Stock',
        'is_virtual' => 'Yes',
        'manage_stock' => '-',
        'stock_data_qty' => '-',
        'stock_data_use_config_min_qty' => '-',
        'stock_data_min_qty' => '-',
        'downloadable_sample' => '-',
        'downloadable_links' => 'default',
        'custom_options' => '-',
        'special_price' => '-',
        'group_price' => '-',
        'tier_price' => '-'
    ];

    /**
     * {inheritdoc}
     */
    protected function _initData()
    {
        parent::_initData();
        $this->_dataConfig = array(
            'constraint' => 'Success',
            'grid_filter' => array('name'),
            'create_url_params' => array(
                'type' => 'downloadable',
                'set' => static::DEFAULT_ATTRIBUTE_SET_ID,
            ),
            'input_prefix' => 'product'
        );

        $data = array(
            'is_virtual' => ['value' => '', 'group' => null], // needed for CURL handler
            'price' => [
                'value' => '1',
                'group' => static::GROUP_PRODUCT_DETAILS
            ]
        );

        $this->_data['fields'] = $data + $this->_data['fields'];

        $this->_repository = Factory::getRepositoryFactory()
            ->getMagentoDownloadableDownloadableProduct($this->_dataConfig, $this->_data);
    }

    /**
     * Create product
     */
    public function persist()
    {
        $id = Factory::getApp()->magentoDownloadableCreateDownloadable($this);
        $this->_data['fields']['id']['value'] = $id;
    }

    public function getCategoryIds()
    {
        return $this->getData('category_ids');
    }

    public function getCost()
    {
        return $this->getData('cost');
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

    public function getGallery()
    {
        return $this->getData('gallery');
    }

    public function getGiftMessageAvailable()
    {
        return $this->getData('gift_message_available');
    }

    public function getGroupPrice()
    {
        return $this->getData('group_price');
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

    public function getIsReturnable()
    {
        return $this->getData('is_returnable');
    }

    public function getLinksExist()
    {
        return $this->getData('links_exist');
    }

    public function getLinksPurchasedSeparately()
    {
        return $this->getData('links_purchased_separately');
    }

    public function getLinksTitle()
    {
        return $this->getData('links_title');
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

    public function getMinimalPrice()
    {
        return $this->getData('minimal_price');
    }

    public function getMsrp()
    {
        return $this->getData('msrp');
    }

    public function getMsrpDisplayActualPriceType()
    {
        return $this->getData('msrp_display_actual_price_type');
    }

    public function getMsrpEnabled()
    {
        return $this->getData('msrp_enabled');
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

    public function getOptionsContainer()
    {
        return $this->getData('options_container');
    }

    public function getPageLayout()
    {
        return $this->getData('page_layout');
    }

    public function getPrice()
    {
        return $this->getData('price');
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

    public function getSamplesTitle()
    {
        return $this->getData('samples_title');
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

    public function getSpecialFromDate()
    {
        return $this->getData('special_from_date');
    }

    public function getSpecialPrice()
    {
        return $this->getData('special_price');
    }

    public function getSpecialToDate()
    {
        return $this->getData('special_to_date');
    }

    public function getStatus()
    {
        return $this->getData('status');
    }

    public function getTaxClassId()
    {
        return $this->getData('tax_class_id');
    }

    public function getThumbnail()
    {
        return $this->getData('thumbnail');
    }

    public function getThumbnailLabel()
    {
        return $this->getData('thumbnail_label');
    }

    public function getTierPrice()
    {
        return $this->getData('tier_price');
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
}
