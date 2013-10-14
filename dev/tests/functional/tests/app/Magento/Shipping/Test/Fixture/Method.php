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

namespace Magento\Shipping\Test\Fixture;

use Mtf\Fixture\DataFixture;

/**
 * Class Method
 * Shipping methods
 *
 * @package Magento\Shipping\Test\Fixture
 */
class Method extends DataFixture
{
    /**
     * {inheritdoc}
     */
    protected function _initData()
    {
        $this->_repository = array(
            'free_shipping' => array(
                'data' => array(
                    'fields' => array(
                        'shipping_service' => 'Free Shipping',
                        'shipping_method' => 'Free'
                    )
                ),
            ),

            'flat_rate' => array(
                'data' => array(
                    'fields' => array(
                        'shipping_service' => 'Flat Rate',
                        'shipping_method' => 'Fixed'
                    )
                ),
            ),
        );

        //Default data set
        $this->switchData('flat_rate');
    }
}
