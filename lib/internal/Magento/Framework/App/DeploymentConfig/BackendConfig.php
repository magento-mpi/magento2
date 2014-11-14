<?php
/**
 * {license_notice}
 *
 * @copyright  {copyright}
 * @license    {license_link}
 */

namespace Magento\Framework\App\DeploymentConfig;

class BackendConfig extends AbstractSegment
{
    /**
     * Key for config.php
     */
    const KEY_FRONTNAME = 'frontName';

    /**
     * Segment key
     */
    const CONFIG_KEY = 'backend';

    /**
     * Constructor
     *
     * @param array $data
     * @throws \InvalidArgumentException
     */
    public function __construct(array $data)
    {
        if (!isset($data[self::KEY_FRONTNAME])) {
            throw new \InvalidArgumentException("No backend frontname provided.");
        }
        if (!preg_match('/^[a-zA-Z0-9_]+$/', $data[self::KEY_FRONTNAME])) {
            throw new \InvalidArgumentException("Invalid backend frontname {$data[self::KEY_FRONTNAME]}");
        }
        parent::__construct($data);
    }

    /**
     * {@inheritdoc}
     */
    public function getKey()
    {
        return self::CONFIG_KEY;
    }
}
