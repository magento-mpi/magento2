<?php
/**
 * {license_notice}
 *
 * @copyright  {copyright}
 * @license    {license_link}
 */

namespace Magento\Framework\App\DeploymentConfig;

class Config implements SegmentInterface
{
    const CONFIG_KEY = 'default_config';

    /**
     * Data
     *
     * @var array
     */
    protected $data;

    /**
     * Constructor
     *
     * @param array $data
     */
    public function __construct(array $data)
    {
        $this->data = $data;
    }

    /**
     * Update data
     *
     * @param string[] $data
     * @return array
     */
    public function update($data)
    {
        if (empty($data)) {
            return $this->data;
        }

        $new = [];
        foreach (array_keys($this->data) as $key) {
            $new[$key] = isset($data[$key]) ? $data[$key] : $this->data[$key];
        }
        return $new;
    }

    /**
     * {@inheritdoc}
     */
    public function getKey()
    {
        return static::CONFIG_KEY;
    }

    /**
     * {@inheritdoc}
     */
    public function getData()
    {
        return $this->data;
    }
}
