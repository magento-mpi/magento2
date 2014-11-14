<?php
/**
 * {license_notice}
 *
 * @copyright  {copyright}
 * @license    {license_link}
 */

namespace Magento\Framework\Module\ModuleList;

use Magento\Framework\App\DeploymentConfig\AbstractSegment;

/**
 * Deployment configuration segment for modules
 */
class DeploymentConfig extends AbstractSegment
{
    /**
     * Segment key
     */
    const CONFIG_KEY = 'modules';

    /**
     * Constructor
     *
     * @param array $data
     * @throws \InvalidArgumentException
     */
    public function __construct(array $data)
    {
        $modules = [];
        foreach ($data as $key => $value) {
            if (!preg_match('/^[A-Z][A-Za-z\d]+_[A-Z][A-Za-z\d]+$/', $key)) {
                throw new \InvalidArgumentException("Incorrect module name: '{$key}'");
            }
            $modules[$key] = (int)$value;
        }

        parent::__construct($modules);
    }

    /**
     * {@inheritdoc}
     */
    public function getKey()
    {
        return self::CONFIG_KEY;
    }
}
