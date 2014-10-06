<?php
/**
 * {license_notice}
 * 
 * @copyright {copyright}
 * @license   {license_link}
 */
namespace Magento\TestFramework\ObjectManager;

class Configurator implements \Magento\Framework\ObjectManager\DynamicConfigInterface
{
    /**
     * Map application initialization params to Object Manager configuration format
     *
     * @return array
     */
    public function getConfiguration()
    {
        return [
            'preferences' => [
                'Magento\Framework\Stdlib\Cookie' => 'Magento\TestFramework\Cookie',
                'Magento\Framework\Stdlib\CookieManager' => 'Magento\TestFramework\CookieManager',
            ]
        ];
    }
}
