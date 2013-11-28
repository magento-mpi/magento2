<?php
/**
 * Store fixture
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Core\Test\Fixture;
use Mtf\Fixture\DataFixture;

class Store extends DataFixture
{
    /**
     * Initialize fixture data
     */
    protected function _initData()
    {
        $this->_data = array(
            'fields' => array(
                'group' => array(
                    'value' => 'Main Website Store',
                    'input' => 'select'
                ),
                'name' => array(
                    'value' => 'DE%isolation%'
                ),
                'code' => array(
                    'value' => 'de%isolation%'
                ),
                'is_active' => array(
                    'value' => 'Enabled',
                    'input' => 'select',
                )
            )
        );
    }
} 