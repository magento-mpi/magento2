<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\GiftRegistry\Test\Repository;

use Mtf\Repository\AbstractRepository;

/**
 * Class GiftRegistry
 * Gift registry repository
 */
class GiftRegistry extends AbstractRepository
{
    /**
     * @construct
     * @param array $defaultConfig
     * @param array $defaultData
     *
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     * @SuppressWarnings(PHPMD.ExcessiveMethodLength)
     */
    public function __construct(array $defaultConfig = [], array $defaultData = [])
    {
        $this->_data['birthday'] = [
            'type_id' => 'Birthday',
            'title' => 'Title%isolation%',
            'message' => 'Test message.',
            'event_country' => 'United States',
            'event_country_region' => 'California',
            'event_date' => ['pattern' => '1/12/2024'],
            'is_public' => 'Public',
            'is_active' => 'Active',
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

        $this->_data['baby_registry'] = [
            'type_id' => 'Baby Registry',
            'title' => 'Title%isolation%',
            'message' => 'Test message.',
            'event_country' => 'United States',
            'event_country_region' => 'California',
            'event_date' => ['pattern' => '1/12/2024'],
            'is_public' => 'Public',
            'is_active' => 'Active',
            'baby_gender' => 'Boy',
            'person_ids' => [
                [
                    'email' => 'email@test%isolation%.com',
                    'firstname' => 'FirstName%isolation%',
                    'lastname' => 'LastName%isolation%',
                    'role' => 'Mother'
                ]
            ],
            'address' => [
                'dataSet' => 'US_address_without_email'
            ]
        ];

        $this->_data['wedding'] = [
            'type_id' => 'Wedding',
            'title' => 'Title%isolation%',
            'message' => 'Test message.',
            'event_country' => 'United States',
            'event_country_region' => 'California',
            'event_date' => ['pattern' => '1/12/2024'],
            'is_public' => 'Public',
            'is_active' => 'Active',
            'event_location' => 'Location%isolation%',
            'number_of_guests' => '50',
            'person_ids' => [
                [
                    'email' => 'email@test%isolation%.com',
                    'firstname' => 'FirstName%isolation%',
                    'lastname' => 'LastName%isolation%',
                    'role' => 'Groom'
                ]
            ],
            'address' => [
                'dataSet' => 'US_address_without_email'
            ]
        ];

        $this->_data['birthday_private'] = [
            'type_id' => 'Birthday',
            'title' => 'Title%isolation%',
            'message' => 'Test message.',
            'event_country' => 'United States',
            'event_country_region' => 'California',
            'event_date' => ['pattern' => '1/12/2024'],
            'is_public' => 'Private',
            'is_active' => 'Active',
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

        $this->_data['baby_registry_inactive'] = [
            'type_id' => 'Baby Registry',
            'title' => 'Title%isolation%',
            'message' => 'Test message.',
            'event_country' => 'United States',
            'event_country_region' => 'California',
            'event_date' => ['pattern' => '1/12/2024'],
            'is_public' => 'Public',
            'is_active' => 'Inactive',
            'baby_gender' => 'Boy',
            'person_ids' => [
                [
                    'email' => 'email@test%isolation%.com',
                    'firstname' => 'FirstName%isolation%',
                    'lastname' => 'LastName%isolation%',
                    'role' => 'Mother'
                ]
            ],
            'address' => [
                'dataSet' => 'US_address_without_email'
            ]
        ];

        $this->_data['wedding_inactive_private'] = [
            'type_id' => 'Wedding',
            'title' => 'Title%isolation%',
            'message' => 'Test message.',
            'event_country' => 'United States',
            'event_country_region' => 'California',
            'event_date' => ['pattern' => '1/12/2024'],
            'is_public' => 'Private',
            'is_active' => 'Inactive',
            'event_location' => 'Location%isolation%',
            'number_of_guests' => '50',
            'person_ids' => [
                [
                    'email' => 'email@test%isolation%.com',
                    'firstname' => 'FirstName%isolation%',
                    'lastname' => 'LastName%isolation%',
                    'role' => 'Groom'
                ]
            ],
            'address' => [
                'dataSet' => 'US_address_without_email'
            ]
        ];
    }
}
