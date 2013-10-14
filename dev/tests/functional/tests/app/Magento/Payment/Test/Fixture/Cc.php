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

namespace Magento\Payment\Test\Fixture;

use Mtf\Fixture\DataFixture;

/**
 * Class Cc
 * Credit cards for checkout
 *
 * @package Magento\Payment\Test\Fixture
 */
class Cc extends DataFixture
{
    /**
     * {inheritdoc}
     */
    protected function _initData()
    {
        $this->_repository = array(
            'visa_default' => array(
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
            ),
            'visa_direct' => array(
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
            ),
            'visa_authorizenet' => array(
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
            ),
        );

        //Default data set
        $this->switchData('visa_default');
    }
}
