<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\GiftRegistry\Test\Fixture\GiftRegistry;

use Mtf\Fixture\FixtureInterface;

/**
 * Class Attributes
 * Prepare Attributes for GiftRegistry
 */
class Attributes implements FixtureInterface
{
    /**
     * Prepared dataSet data
     *
     * @var array
     */
    protected $data;

    /**
     * Data set configuration settings
     *
     * @var array
     */
    protected $params;

    /**
     * Constructor
     *
     * @param array $params [optional]
     * @param array $data [optional]
     */
    public function __construct(array $params, array $data = [])
    {
        $this->params = $params;
        if (isset($data['preset'])) {
            $this->data = $this->getPreset($data['preset']);
        } else {
            $this->data = $data;
        }
    }

    /**
     * Persist attribute options
     *
     * @return void
     */
    public function persist()
    {
        //
    }

    /**
     * Return prepared data set
     *
     * @param string|null $key [optional]
     * @return mixed
     *
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function getData($key = null)
    {
        return $this->data;
    }

    /**
     * Return data set configuration settings
     *
     * @return array
     */
    public function getDataConfig()
    {
        return $this->params;
    }

    /**
     * Preset for Attribute manage options
     *
     * @param string $name
     * @return array|null
     */
    protected function getPreset($name)
    {
        $preset = [
            'text' => [
                [
                    'code' => 'text_%isolation%',
                    'type' => 'text',
                    'group' => 'Event Information',
                    'label' => 'text_%isolation%',
                    'is_required' => 'Yes',
                    'sort_order' => '10',
                ]
            ],
            'select' => [
                [
                    'code' => 'select_%isolation%',
                    'type' => 'select',
                    'group' => 'Gift Registry Properties',
                    'label' => 'select_%isolation%',
                    'is_required' => 'Yes',
                    'sort_order' => '20',
                    'options' => [
                        [
                            'code' => 'code_%isolation%',
                            'label' => 'label1_%isolation%',
                            'is_default' => 'Yes'
                        ],
                        [
                            'code' => 'code_%isolation%',
                            'label' => 'label2_%isolation%',
                            'is_default' => 'No'
                        ],
                        [
                            'code' => 'code_%isolation%',
                            'label' => 'label3_%isolation%',
                            'is_default' => 'No'
                        ]
                    ],
                ]
            ],
            'date' => [
                [
                    'code' => 'date_%isolation%',
                    'type' => 'date',
                    'group' => 'Privacy Settings',
                    'label' => 'date_%isolation%',
                    'is_required' => 'Yes',
                    'sort_order' => '30',
                    'date_format' => 'Full'
                ]
            ],
            'country' => [
                [
                    'code' => 'contry_%isolation%',
                    'type' => 'country',
                    'group' => 'Recipients Information',
                    'label' => 'country_%isolation%',
                    'is_required' => 'No',
                    'sort_order' => '40',
                    'show_region' => 'Yes'
                ]
            ],
            'event_date' => [
                [
                    'type' => 'Event Date',
                    'label' => 'eventdate_%isolation%',
                    'is_required' => 'No',
                    'sort_order' => '50',
                    'is_searcheable' => 'Yes',
                    'is_listed' => 'No',
                    'date_format' => 'Medium'
                ]
            ],
            'event_country' => [
                [
                    'type' => 'Event Country',
                    'label' => 'eventcountry_%isolation%',
                    'is_required' => 'No',
                    'sort_order' => '60',
                    'is_searcheable' => 'Yes',
                    'is_listed' => 'No',
                    'show_region' => 'No'
                ]
            ],
            'event_location' => [
                [
                    'type' => 'Event Location',
                    'label' => 'eventlocation_%isolation%',
                    'is_required' => 'No',
                    'sort_order' => '70',
                    'is_searcheable' => 'No',
                    'is_listed' => 'No',
                ]
            ],
            'role' => [
                [
                    'code' => 'role_%isolation%',
                    'type' => 'role',
                    'label' => 'role_%isolation%',
                    'is_required' => 'No',
                    'sort_order' => '80',
                    'is_searcheable' => 'No',
                    'is_listed' => 'No',
                    'options' => [
                        [
                            'code' => 'code_%isolation%',
                            'label' => 'label1_%isolation%',
                            'is_default' => 'Yes'
                        ],
                        [
                            'code' => 'code_%isolation%',
                            'label' => 'label2_%isolation%',
                            'is_default' => 'No'
                        ],
                        [
                            'code' => 'code_%isolation%',
                            'label' => 'label3_%isolation%',
                            'is_default' => 'No'
                        ]
                    ],
                ]
            ],
        ];

        if (!isset($preset[$name])) {
            return null;
        }

        return $preset[$name];
    }
}
