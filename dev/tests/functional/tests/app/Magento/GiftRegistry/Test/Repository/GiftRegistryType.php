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
 * Class GiftRegistryType
 * Gift registry type repository
 */
class GiftRegistryType extends AbstractRepository
{
    /**
     * @construct
     * @param array $defaultConfig
     * @param array $defaultData
     *
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function __construct(array $defaultConfig = [], array $defaultData = [])
    {
        $this->_data['text'] = [
            'code' => 'code_%isolation%',
            'label' => 'gift_registry_label%isolation%',
            'is_listed' => 'Yes',
            'attributes' => ['preset' => 'text'],
        ];

        $this->_data['select'] = [
            'code' => 'code_%isolation%',
            'label' => 'gift_registry_select%isolation%',
            'is_listed' => 'Yes',
            'attributes' => ['preset' => 'select'],
        ];

        $this->_data['date'] = [
            'code' => 'code_%isolation%',
            'label' => 'gift_registry_date%isolation%',
            'is_listed' => 'Yes',
            'attributes' => ['preset' => 'date'],
        ];

        $this->_data['country'] = [
            'code' => 'code_%isolation%',
            'label' => 'gift_registry_label%isolation%',
            'is_listed' => 'Yes',
            'attributes' => ['preset' => 'country'],
        ];

        $this->_data['event_date'] = [
            'code' => 'code_%isolation%',
            'label' => 'gift_registry_event_date%isolation%',
            'is_listed' => 'Yes',
            'attributes' => ['preset' => 'country'],
        ];

        $this->_data['event_country'] = [
            'code' => 'code_%isolation%',
            'label' => 'gift_registry_event_country%isolation%',
            'is_listed' => 'Yes',
            'attributes' => ['preset' => 'country'],
        ];

        $this->_data['event_location'] = [
            'code' => 'code_%isolation%',
            'label' => 'gift_registry_event_location%isolation%',
            'is_listed' => 'Yes',
            'attributes' => ['preset' => 'country'],
        ];

        $this->_data['role'] = [
            'code' => 'code_%isolation%',
            'label' => 'gift_registry_rolel%isolation%',
            'is_listed' => 'Yes',
            'attributes' => ['preset' => 'role'],
        ];
    }
}
