<?php
/**
 * {license_notice}
 *
 * @copyright  {copyright}
 * @license    {license_link}
 */

namespace Magento\Framework\Module\ModuleList;

use Magento\Framework\App\DeploymentConfig\SegmentInterface;

/**
 * Deployment configuration segment for modules
 */
class DeploymentConfig implements SegmentInterface
{
    /**
     * Segment key
     */
    const CONFIG_KEY = 'modules';

    /**
     * Data -- list of enabled modules
     *
     * @var array
     */
    private $data;

    /**
     * Constructor
     *
     * @param array $data
     * @throws \InvalidArgumentException
     */
    public function __construct(array $data)
    {
        $this->data = [];
        foreach ($data as $key => $value) {
            if (!preg_match('/^[A-Z][A-Za-z\d]+_[A-Z][A-Za-z\d]+$/', $key)) {
                throw new \InvalidArgumentException("Incorrect module name: '{$key}'");
            }
            $this->data[$key] = (int)$value;
        }
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
