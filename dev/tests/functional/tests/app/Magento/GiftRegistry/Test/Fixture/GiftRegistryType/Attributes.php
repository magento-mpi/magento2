<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\GiftRegistry\Test\Fixture\GiftRegistryType;

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
     * @param array $params
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
     *
     * @SuppressWarnings(PHPMD.ExcessiveMethodLength)
     */
    protected function getPreset($name)
    {
        $preset = [
            'text' => [
                [
                    'code' => 'text_%isolation%',
                    'type' => 'Custom Types/Text',
                    'group' => 'Event Information',
                    'label' => 'text_%isolation%',
                    'is_required' => 'Yes',
                    'sort_order' => '10',
                    'is_deleted' => ''
                ]
            ],
            'select' => [
                [
                    'code' => 'select_%isolation%',
                    'type' => 'Custom Types/Select',
                    'group' => 'Gift Registry Properties',
                    'label' => 'select_%isolation%',
                    'is_required' => 'Yes',
                    'sort_order' => '20',
                    'is_deleted' => '',
                    'options' => [
                        [
                            'code' => 'code1_%isolation%',
                            'label' => 'label1_%isolation%',
                            'is_default' => 'Yes',
                            'is_deleted' => ''
                        ],
                        [
                            'code' => 'code2_%isolation%',
                            'label' => 'label2_%isolation%',
                            'is_default' => 'No',
                            'is_deleted' => ''
                        ],
                        [
                            'code' => 'code3_%isolation%',
                            'label' => 'label3_%isolation%',
                            'is_default' => 'No',
                            'is_deleted' => ''
                        ]
                    ],
                ]
            ],
            'date' => [
                [
                    'code' => 'date_%isolation%',
                    'type' => 'Custom Types/Date',
                    'group' => 'Privacy Settings',
                    'label' => 'date_%isolation%',
                    'is_required' => 'Yes',
                    'sort_order' => '30',
                    'date_format' => 'Full',
                    'is_deleted' => ''
                ]
            ],
            'country' => [
                [
                    'code' => 'contry_%isolation%',
                    'type' => 'Custom Types/Country',
                    'group' => 'Recipients Information',
                    'label' => 'country_%isolation%',
                    'is_required' => 'No',
                    'sort_order' => '40',
                    'show_region' => 'Yes',
                    'is_deleted' => ''
                ]
            ],
            'event_date' => [
                [
                    'type' => 'Static Types/Event Date',
                    'label' => 'eventdate_%isolation%',
                    'is_required' => 'No',
                    'sort_order' => '50',
                    'is_searcheable' => 'Yes',
                    'is_listed' => 'No',
                    'date_format' => 'Medium',
                    'is_deleted' => ''
                ]
            ],
            'event_country' => [
                [
                    'type' => 'Static Types/Event Country',
                    'label' => 'eventcountry_%isolation%',
                    'is_required' => 'No',
                    'sort_order' => '60',
                    'is_searcheable' => 'Yes',
                    'is_listed' => 'No',
                    'show_region' => 'No',
                    'is_deleted' => ''
                ]
            ],
            'event_location' => [
                [
                    'type' => 'Static Types/Event Location',
                    'label' => 'eventlocation_%isolation%',
                    'is_required' => 'No',
                    'sort_order' => '70',
                    'is_searcheable' => 'No',
                    'is_listed' => 'No',
                    'is_deleted' => ''
                ]
            ],
            'role' => [
                [
                    'type' => 'Static Types/Role',
                    'label' => 'role_%isolation%',
                    'is_required' => 'No',
                    'sort_order' => '80',
                    'is_searcheable' => 'No',
                    'is_listed' => 'No',
                    'is_deleted' => '',
                    'options' => [
                        [
                            'code' => 'code1_%isolation%',
                            'label' => 'label1_%isolation%',
                            'is_default' => 'Yes',
                            'is_deleted' => ''
                        ],
                        [
                            'code' => 'code2_%isolation%',
                            'label' => 'label2_%isolation%',
                            'is_default' => 'No',
                            'is_deleted' => ''
                        ],
                        [
                            'code' => 'code3_%isolation%',
                            'label' => 'label3_%isolation%',
                            'is_default' => 'No',
                            'is_deleted' => ''
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
