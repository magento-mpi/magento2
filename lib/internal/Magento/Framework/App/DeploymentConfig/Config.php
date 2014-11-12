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
    protected function update(array $data)
    {
        $isSet = function ($value) {
            return isset($value);
        };
        // get rid of null values
        $data = array_filter($data, $isSet);
        if (empty($data)) {
            return $this->data;
        }

        $new = array_replace_recursive($this->data, $data);
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
