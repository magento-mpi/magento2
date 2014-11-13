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
        // get rid of null values
        $data = $this->filterArray($data);
        if (empty($data)) {
            return $this->data;
        }

        $new = array_replace_recursive($this->data, $data);
        return $new;
    }

    /**
     * Filter an array recursively
     *
     * @param array $data
     * @return array
     */
    private function filterArray(array $data)
    {
        foreach ($data as $key => $value) {
            if (is_array($value)) {
                $data[$key] = $this->filterArray($value);
            } else if (!isset($value)) {
                unset($data[$key]);
            }
        }
        return $data;
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
