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
        $this->_data['visa_3d_secure_valid'] = $this->_getVisa3dSecureValid();
        $this->_data['visa_3d_secure_invalid'] = $this->_getVisa3dSecureInvalid();
        $this->_data['visa_payflow'] = $this->_getVisaPayflow();
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

    protected function _getVisaPayflow()
    {
        return array(
            'config' => array(
                'constraint' => 'Success',
            ),
            'data' => array(
                'fields' => array(
                    'credit_card_number' => array(
                        'value' => '4111111111111111'
                    ),
                    'expiration_month' => array(
                        'value' => '01',
                    ),
                    'expiration_year' => array(
                        'value' => date('y') + 1
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

    protected function _getVisa3dSecureValid()
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
                        'value' => '4000000000000002'
                    ),
                    'expiration_month' => array(
                        'value' => '01 - January',
                        'input' => 'select'
                    ),
                    'expiration_year' => array(
                        'value' => date('Y') + 2,
                        'input' => 'select'
                    ),
                    'credit_card_cvv' => array(
                        'value' => '123'
                    ),
                ),
                'validation' => array(
                    'password' => array(
                        'value' => '1234'
                    )
                )
            )
        );
    }

    protected function _getVisa3dSecureInvalid()
    {
        $invalidData = array(
            'data' => array(
                'fields' => array(
                    'credit_card_number' => array(
                        'value' => '4000000000000010'
                    ),
                ),
            )
        );

        return array_replace_recursive($this->_getVisa3dSecureValid(), $invalidData);
    }
}
