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

namespace Magento\Shipping\Test\Repository;

use Mtf\Repository\AbstractRepository;

/**
 * Class Method Repository
 * Shipping methods
 *
 * @package Magento\Shipping\Test\Fixture
 */
class Method extends AbstractRepository
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

        $this->_data['free_shipping'] = $this->_getFreeShipping();
        $this->_data['flat_rate'] = $this->_getFlatRate();
    }

    protected function _getFreeShipping()
    {
        return array(
            'data' => array(
                'fields' => array(
                    'shipping_service' => 'Free Shipping',
                    'shipping_method' => 'Free'
                )
            )
        );
    }

    protected function _getFlatRate()
    {
        return array(
            'data' => array(
                'fields' => array(
                    'shipping_service' => 'Flat Rate',
                    'shipping_method' => 'Fixed'
                )
            )
        );
    }
}
