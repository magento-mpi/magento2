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

namespace Magento\Payment\Test\Repository;

use Mtf\Repository\AbstractRepository;

/**
 * Class Cc Repository
 * Credit cards for checkout
 *
 * @package Magento\Payment\Test\Repository
 */
class Cc extends AbstractRepository
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

        $this->_data['visa_default'] = $this->_getVisaDefault();
        $this->_data['visa_direct'] = $this->_getVisaDirect();
        $this->_data['visa_authorizenet'] = $this->_getVisaAuthorizeNet();
    }

    protected function _getVisaDefault()
    {
        return array(
            'config' => array(
                'constraint' => 'Success',
            ),
            'data' => array(
                'fields' => array(
                    'credit_card_type' => array(
                        'value' => 'Visa',
                        'input' => 'select'
                    ),
                    'credit_card_number' => array(
                        'value' => '4111111111111111'
                    ),
                    'expiration_month' => array(
                        'value' => '01 - January',
                        'input' => 'select'
                    ),
                    'expiration_year' => array(
                        'value' => date('Y') + 1,
                        'input' => 'select'
                    ),
                    'credit_card_cvv' => array(
                        'value' => '123'
                    )
                )
            )
        );
    }

    protected function _getVisaDirect()
    {
        return array(
            'config' => array(
                'constraint' => 'Success',
            ),
            'data' => array(
                'fields' => array(
                    'credit_card_type' => array(
                        'value' => 'Visa',
                        'input' => 'select'
                    ),
                    'credit_card_number' => array(
                        'value' => '4617747819866651'
                    ),
                    'expiration_month' => array(
                        'value' => '01 - January',
                        'input' => 'select'
                    ),
                    'expiration_year' => array(
                        'value' => date('Y') + 1,
                        'input' => 'select'
                    ),
                    'credit_card_cvv' => array(
                        'value' => '123'
                    )
                )
            )
        );
    }

    protected function _getVisaAuthorizeNet()
    {
        return array(
            'config' => array(
                'constraint' => 'Success',
            ),
            'data' => array(
                'fields' => array(
                    'credit_card_type' => array(
                        'value' => 'Visa',
                        'input' => 'select'
                    ),
                    'credit_card_number' => array(
                        'value' => '4007000000027'
                    ),
                    'expiration_month' => array(
                        'value' => '01 - January',
                        'input' => 'select'
                    ),
                    'expiration_year' => array(
                        'value' => date('Y') + 1,
                        'input' => 'select'
                    ),
                    'credit_card_cvv' => array(
                        'value' => '123'
                    )
                )
            )
        );
    }
}
