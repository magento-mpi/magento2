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
     * Segment key
     */
    const CONFIG_KEY = 'crypt';

    /**
     * Encryption key length
     */
    const KEY_LENGTH = 32;

    /**
     * Array key
     */
    const KEY_ENCRYPTION_KEY = 'key';

    /**
     * Constructor
     *
     * @param array $data
     * @throws \InvalidArgumentException
     */
    public function __construct(array $data)
    {
        if (strlen($data[self::KEY_ENCRYPTION_KEY]) != self::KEY_LENGTH) {
            throw new \InvalidArgumentException("Invalid encryption key: '{$data[self::KEY_ENCRYPTION_KEY]}'");
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
