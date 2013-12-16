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
        // Shipping carriers
        $this->_data['dhlint_eu'] = $this->_getDhlIntEU();
        $this->_data['dhlint_uk'] = $this->_getDhlIntUK();
        $this->_data['fedex'] = $this->_getFedex();
        $this->_data['ups'] = $this->_getUps();
        $this->_data['usps'] = $this->_getUsps();
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

    protected function _getDhlIntEU()
    {
        return array(
            'data' => array(
                'fields' => array(
                    'shipping_service' => 'DHL',
                    'shipping_method' => 'Express worldwide'
                )
            )
        );
    }

    protected function _getDhlIntUK()
    {
        return array(
            'data' => array(
                'fields' => array(
                    'shipping_service' => 'DHL',
                    'shipping_method' => 'Domestic express'
                )
            )
        );
    }

    protected function _getFedex()
    {
        return array(
            'data' => array(
                'fields' => array(
                    'shipping_service' => 'Federal Express',
                    'shipping_method' => 'Ground'
                )
            )
        );
    }

    protected function _getUps()
    {
        return array(
            'data' => array(
                'fields' => array(
                    'shipping_service' => 'United Parcel Service',
                    'shipping_method' => 'Ground'
                )
            )
        );
    }

    protected function _getUsps()
    {
        return array(
            'data' => array(
                'fields' => array(
                    'shipping_service' => 'United States Postal Service',
                    'shipping_method' => 'Mail'  /** @todo change to 'Priority Mail' when usps config is updated */
                )
            )
        );
    }
}
