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
 * Class Default Store Repository
 *
 * @package namespace Magento\User\Test\Repository
 */
class DefaultStore extends AbstractRepository
{
    /**
     * {@inheritdoc}
     */
    public function __construct(array $defaultConfig, array $defaultData)
    {
        $this->_data['default'] = array(
            'config' => $defaultConfig,
            'data' => $defaultData
        );

        $this->_data['default_store']['data'] = array(
            'name'             => 'Store%isolation%',
            'root_category_id' => 2,
            'website_id'       => 1,
        );
    }
}