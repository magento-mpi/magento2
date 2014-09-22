<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\GiftRegistry\Test\Fixture;

use Mtf\Fixture\InjectableFixture;

/**
 * Class GiftRegistry
 * Fixture for gift registry
 *
 * @SuppressWarnings(PHPMD.TooManyFields)
 */
class GiftRegistry extends InjectableFixture
{
    /**
     * @var string
     */
    protected $repositoryClass = 'Magento\GiftRegistry\Test\Repository\GiftRegistry';

    /**
     * @var string
     */
    protected $handlerInterface = 'Magento\GiftRegistry\Test\Handler\GiftRegistry\GiftRegistryInterface';

    protected $defaultDataSet = [
        'type_id' => 'Birthday',
        'title' => 'Title%isolation%',
        'message' => 'Test message.',
        'event_country' => 'United States',
        'event_country_region' => 'California',
        'event_date' => ['pattern' => '1/12/2024'],
        'is_active' => 'Active',
        'is_public' => 'Public',
        'person_ids' => [
            [
                'email' => 'email@test%isolation%.com',
                'firstname' => 'FirstName%isolation%',
                'lastname' => 'LastName%isolation%',
            ]
        ],
        'address' => [
            'dataSet' => 'US_address_without_email'
        ]
    ];

    protected $entity_id = [
        'attribute_code' => 'entity_id',
        'backend_type' => 'int',
        'is_required' => '',
        'default_value' => '0',
        'input' => '',
    ];

    protected $type_id = [
        'attribute_code' => 'type_id',
        'backend_type' => 'int',
        'is_required' => '',
        'default_value' => '0',
        'input' => '',
    ];

    protected $customer_id = [
        'attribute_code' => 'customer_id',
        'backend_type' => 'int',
        'is_required' => '',
        'default_value' => '0',
        'input' => '',
    ];

    protected $website_id = [
        'attribute_code' => 'website_id',
        'backend_type' => 'smallint',
        'is_required' => '',
        'default_value' => '0',
        'input' => '',
    ];

    protected $is_public = [
        'attribute_code' => 'is_public',
        'backend_type' => 'smallint',
        'is_required' => '',
        'default_value' => '1',
        'input' => '',
    ];

    protected $url_key = [
        'attribute_code' => 'url_key',
        'backend_type' => 'varchar',
        'is_required' => '',
        'default_value' => '',
        'input' => '',
    ];

    protected $title = [
        'attribute_code' => 'title',
        'backend_type' => 'varchar',
        'is_required' => '',
        'default_value' => '',
        'input' => '',
    ];

    protected $message = [
        'attribute_code' => 'message',
        'backend_type' => 'text',
        'is_required' => '',
        'default_value' => '',
        'input' => '',
    ];

    protected $shipping_address = [
        'attribute_code' => 'shipping_address',
        'backend_type' => 'blob',
        'is_required' => '',
        'default_value' => '',
        'input' => '',
    ];

    protected $custom_values = [
        'attribute_code' => 'custom_values',
        'backend_type' => 'text',
        'is_required' => '',
        'default_value' => '',
        'input' => '',
    ];

    protected $is_active = [
        'attribute_code' => 'is_active',
        'backend_type' => 'smallint',
        'is_required' => '',
        'default_value' => '0',
        'input' => '',
    ];

    protected $created_at = [
        'attribute_code' => 'created_at',
        'backend_type' => 'timestamp',
        'is_required' => '',
        'default_value' => '',
        'input' => '',
    ];

    protected $event_date = [
        'attribute_code' => 'event_date',
        'backend_type' => 'date',
        'source' => 'Magento\Backend\Test\Fixture\Date',
    ];

    protected $event_country = [
        'attribute_code' => 'event_country',
        'backend_type' => 'varchar',
        'is_required' => '',
        'default_value' => '',
        'input' => '',
    ];

    protected $event_country_region = [
        'attribute_code' => 'event_country_region',
        'backend_type' => 'int',
        'is_required' => '',
        'default_value' => '',
        'input' => '',
    ];

    protected $event_country_region_text = [
        'attribute_code' => 'event_country_region_text',
        'backend_type' => 'varchar',
        'is_required' => '',
        'default_value' => '',
        'input' => '',
    ];

    protected $event_location = [
        'attribute_code' => 'event_location',
        'backend_type' => 'varchar',
        'is_required' => '',
        'default_value' => '',
        'input' => '',
    ];

    protected $person_ids = [
        'attribute_code' => 'person_ids',
        'backend_type' => 'int',
        'is_required' => '1',
        'default_value' => '',
        'input' => '',
    ];

    protected $number_of_guests = [
        'attribute_code' => 'number_of_guests',
        'backend_type' => 'virtual',
    ];

    protected $baby_gender = [
        'attribute_code' => 'baby_gender',
        'backend_type' => 'virtual',
    ];

    protected $address = [
        'attribute_code' => 'address',
        'backend_type' => 'virtual',
        'source' => 'Magento\GiftRegistry\Test\Fixture\GiftRegistry\Address',
    ];

    public function getEntityId()
    {
        return $this->getData('entity_id');
    }

    public function getTypeId()
    {
        return $this->getData('type_id');
    }

    public function getCustomerId()
    {
        return $this->getData('customer_id');
    }

    public function getWebsiteId()
    {
        return $this->getData('website_id');
    }

    public function getIsPublic()
    {
        return $this->getData('is_public');
    }

    public function getUrlKey()
    {
        return $this->getData('url_key');
    }

    public function getTitle()
    {
        return $this->getData('title');
    }

    public function getMessage()
    {
        return $this->getData('message');
    }

    public function getShippingAddress()
    {
        return $this->getData('shipping_address');
    }

    public function getCustomValues()
    {
        return $this->getData('custom_values');
    }

    public function getIsActive()
    {
        return $this->getData('is_active');
    }

    public function getCreatedAt()
    {
        return $this->getData('created_at');
    }

    public function getEventDate()
    {
        return $this->getData('event_date');
    }

    public function getEventCountry()
    {
        return $this->getData('event_country');
    }

    public function getEventCountryRegion()
    {
        return $this->getData('event_country_region');
    }

    public function getEventCountryRegionText()
    {
        return $this->getData('event_country_region_text');
    }

    public function getEventLocation()
    {
        return $this->getData('event_location');
    }

    public function getPersonIds()
    {
        return $this->getData('person_ids');
    }

    public function getNumberOfGuests()
    {
        return $this->getData('number_of_guests');
    }

    public function getBabyGender()
    {
        return $this->getData('baby_gender');
    }

    public function getAddress()
    {
        return $this->getData('address');
    }
}
