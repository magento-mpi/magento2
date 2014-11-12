<?php
/**
 * {license_notice}
 *
 * @copyright  {copyright}
 * @license    {license_link}
 */

namespace Magento\Framework\App\DeploymentConfig;

class ResourceConfig extends Config
{
    const KEY_CONNECTION = 'connection';

    const CONFIG_KEY = 'resource';

    /**
     * Constructor
     *
     * @param array $data
     */
    public function __construct(array $data = [])
    {
        $this->data = [
            'default_setup' => [
                'name' => 'default_setup',
                self::KEY_CONNECTION => 'default',
            ]
        ];

        parent::__construct($this->update($data));
    }
}
