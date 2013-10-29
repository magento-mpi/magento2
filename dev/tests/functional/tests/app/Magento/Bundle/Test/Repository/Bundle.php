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

namespace Magento\Bundle\Test\Repository;

use Mtf\Repository\AbstractRepository;

/**
 * Class Product Repository
 *
 * @package Magento\Catalog\Test\Repository
 */
class Bundle extends AbstractRepository
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

        $this->_data['bundle_fixed_required'] = $this->_data['default'];
        $this->_data['bundle_fixed']['data']['category_name'] = '%category::getCategoryName%';
        $this->_data['bundle_fixed']['data']['fields']['category_ids'] = array(
            'value' => array('%category::getCategoryId%')
        );
    }
}
