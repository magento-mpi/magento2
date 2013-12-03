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

namespace Magento\GiftCard\Test\Repository;

use Mtf\Repository\AbstractRepository;

/**
 * Class Product Repository
 *
 * @package Magento\GiftCard\Test\Repository
 */
class GiftCard extends AbstractRepository
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

        $this->_data['virtual_open_amount'] = $this->_data['default'];
        $this->_data['virtual_open_amount']['data']['category_name'] = '%category::getCategoryName%';
    }
}
