<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_ImportExport
 * @subpackage  integration_tests
 * @copyright   {copyright}
 * @license     {license_link}
 */

$bunches = array(
    0 => array(
        'entity'         => 'customer',
        'behavior'       => 'v2_update',
        'data'           => array(
            0 =>
            array(
                'email'                       => 'AnthonyANealy@magento.com',
                '_website'                    => 'base',
                '_store'                      => 'admin',
                'confirmation'                => NULL,
                'created_at'                  => '05-06-12 15:53',
                'created_in'                  => 'Admin',
                'default_billing'             => '1',
                'default_shipping'            => '1',
                'disable_auto_group_change'   => '0',
                'dob'                         => '13-06-1984',
                'firstname'                   => 'Anthony',
                'gender'                      => 'Male',
                'group_id'                    => '1',
                'lastname'                    => 'Nealy',
                'middlename'                  => 'A.',
                'password_hash'               => '6a9c9bfb2ba88a6ad2a64e7402df44a763e0c48cd21d7af9e7e796cd4677ee28:RF',
                'prefix'                      => NULL,
                'reward_update_notification'  => '1',
                'reward_warning_notification' => '1',
                'rp_token'                    => NULL,
                'rp_token_created_at'         => NULL,
                'store_id'                    => '0',
                'suffix'                      => NULL,
                'taxvat'                      => NULL,
                'website_id'                  => '1',
                'password'                    => NULL,
            ),
            1 =>
            array(
                'email'                       => 'LoriBBanks@magento.com',
                '_website'                    => 'admin',
                '_store'                      => 'admin',
                'confirmation'                => NULL,
                'created_at'                  => '05-06-12 15:59',
                'created_in'                  => 'Admin',
                'default_billing'             => '3',
                'default_shipping'            => '3',
                'disable_auto_group_change'   => '0',
                'dob'                         => NULL,
                'firstname'                   => 'Lori',
                'gender'                      => 'Female',
                'group_id'                    => '1',
                'lastname'                    => 'Banks',
                'middlename'                  => NULL,
                'password_hash'               => '7ad6dbdc83d3e9f598825dc58b84678c7351e4281f6bc2b277a32dcd88b9756b:pz',
                'prefix'                      => NULL,
                'reward_update_notification'  => '1',
                'reward_warning_notification' => '1',
                'rp_token'                    => NULL,
                'rp_token_created_at'         => NULL,
                'store_id'                    => '0',
                'suffix'                      => NULL,
                'taxvat'                      => NULL,
                'website_id'                  => '0',
                'password'                    => NULL,
            ),
        )

    ),
    1 => array(
        'entity'         => 'customer',
        'behavior'       => 'v2_update',
        'data'           => array(
            0 =>
            array(
                'email'                       => 'BetsyHParker@magento.com',
                '_website'                    => 'base',
                '_store'                      => 'admin',
                'confirmation'                => NULL,
                'created_at'                  => '05-06-12 16:13',
                'created_in'                  => 'Admin',
                'default_billing'             => '4',
                'default_shipping'            => '4',
                'disable_auto_group_change'   => '0',
                'dob'                         => NULL,
                'firstname'                   => 'Betsy',
                'gender'                      => 'Female',
                'group_id'                    => '1',
                'lastname'                    => 'Parker',
                'middlename'                  => 'H.',
                'password_hash'               => '145d12bfff8a6a279eb61e277e3d727c0ba95acc1131237f1594ddbb7687a564:l1',
                'prefix'                      => NULL,
                'reward_update_notification'  => '1',
                'reward_warning_notification' => '1',
                'rp_token'                    => NULL,
                'rp_token_created_at'         => NULL,
                'store_id'                    => '0',
                'suffix'                      => NULL,
                'taxvat'                      => NULL,
                'website_id'                  => '2',
                'password'                    => NULL,
            ),
        )

    )
);

$importDataResource = Mage::getResourceModel('Magento\ImportExport\Model\Resource\Import\Data');

foreach ($bunches as $bunch) {
    $importDataResource->saveBunch($bunch['entity'], $bunch['behavior'], $bunch['data']);
}

Mage::unregister('_fixture/Magento_ImportExport_Import_Data');
Mage::register('_fixture/Magento_ImportExport_Import_Data', $bunches);
