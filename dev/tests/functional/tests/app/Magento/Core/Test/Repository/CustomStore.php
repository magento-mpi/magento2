<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Core\Test\Repository;

use Mtf\Repository\AbstractRepository;

/**
 * Class Custom Store Repository
 *
 * @package namespace Magento\User\Test\Repository
 */
class CustomStore extends AbstractRepository
{
    /**
     * {@inheritdoc}
     */
    public function __construct(array $defaultConfig = array(), array $defaultData = array())
    {
        $this->_data['default'] = array(
            'config' => $defaultConfig,
            'data' => $defaultData
        );

        $this->_data['custom_store']['data'] = array(
            'fields' => array(
                'group_id' => array(
                    'value' => '%store_group%',
                    'input' => 'select'
                ),
                'name' => array(
                    'value' => 'StoreView%isolation%'
                ),
                'code' => array(
                    'value' => 'storeview%isolation%'
                ),
                'is_active' => array(
                    'value' => '1',
                    'input' => 'select',
                )
            )
        );
    }
}

