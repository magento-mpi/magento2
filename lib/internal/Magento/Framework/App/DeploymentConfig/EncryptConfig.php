<?php
/**
 * {license_notice}
 *
 * @copyright  {copyright}
 * @license    {license_link}
 */

namespace Magento\Framework\App\DeploymentConfig;

class EncryptConfig extends AbstractSegment
{
    /**
     * Array Key for encryption key in deployment config file
     */
    const KEY_ENCRYPTION_KEY = 'key';

    /**
     * Encryption key length
     */
    const KEY_LENGTH = 32;

    /**
     * Segment key
     */
    const CONFIG_KEY = 'crypt';

    /**
     * Constructor
     *
     * @param array $data
     * @throws \InvalidArgumentException
     */
    public function __construct(array $data)
    {
        if (!isset($data[self::KEY_ENCRYPTION_KEY])) {
            throw new \InvalidArgumentException('No encryption key provided');
        }
        foreach (explode("\n", $data[self::KEY_ENCRYPTION_KEY]) as $key) {
            if (strlen($key) != self::KEY_LENGTH ||
                !preg_match('/^[a-zA-Z0-9]+$/', $key)
            ) {
                throw new \InvalidArgumentException("Invalid encryption key: '{$key}'");
            }
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
