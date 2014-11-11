<?php
/**
 * {license_notice}
 *
 * @copyright  {copyright}
 * @license    {license_link}
 */

namespace Magento\Framework\App\DeploymentConfig;

class ResourceConfig implements SegmentInterface
{
    const KEY_CONNECTION = 'connection';

    private $data = [
        'default_setup' => [
            'name' => 'default_setup',
            self::KEY_CONNECTION => 'default',
        ]
    ];

    const CONFIG_KEY = 'resource';

    /**
     * Constructor
     *
     * @param array $data
     */
    public function __construct(array $data = [])
    {
        if (!empty($data)) {
            $this->update($data);
        }
    }

    /**
     * Update data
     *
     * @param string[] $data
     * @return void
     */
    public function update($data)
    {
        $new = [];
        foreach (array_keys($this->data) as $key) {
            $new[$key] =
                isset($data[$key]) ? $data[$key] : $this->data[$key];
        }
        $this->data = $new;
    }

    /**
     * {@inheritdoc}
     */
    public function getKey()
    {
        return self::CONFIG_KEY;
    }

    /**
     * {@inheritdoc}
     */
    public function getData()
    {
        return $this->data;
    }
}