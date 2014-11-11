<?php
/**
 * {license_notice}
 *
 * @copyright  {copyright}
 * @license    {license_link}
 */

namespace Magento\Framework\App\DeploymentConfig;

class EncryptConfig implements SegmentInterface
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
     * Data -- encryption key
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
        if (strlen($data[self::KEY_ENCRYPTION_KEY]) != self::KEY_LENGTH) {
            throw new \InvalidArgumentException("Invalid encryption key: '{$data[self::KEY_ENCRYPTION_KEY]}'");
        }
        $this->data[self::KEY_ENCRYPTION_KEY] = $data[self::KEY_ENCRYPTION_KEY];
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
