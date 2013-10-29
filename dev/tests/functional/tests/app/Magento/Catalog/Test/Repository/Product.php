<?php
/**
 * {license_notice}
 *
 * @category    Mtf
 * @package     Mtf
 * @subpackage  functional_tests
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Catalog\Test\Repository;

use Mtf\Repository\AbstractRepository;

/**
 * Class Product Repository
 *
 * @package Magento\Catalog\Test\Repository
 */
class Product extends AbstractRepository
{
    /**
     * {inheritdoc}
     */
    public function __construct(array $defaultConfig, array $defaultData)
    {
        $this->_data['default'] = array(
            'config' => $defaultConfig,
            'data' => $defaultData
        );

        $this->_data['simple_required'] = array(
            'config' => $defaultConfig,
            'data' => $defaultData
        );

        $this->_data['simple'] = $this->_data['simple_required'];
        $this->_data['simple']['data']['category_name'] = '%category::getCategoryName%';
        $this->_data['simple']['data']['fields']['category_ids'] = array(
            'value' => array('%category::getCategoryId%')
        );
    }
}
