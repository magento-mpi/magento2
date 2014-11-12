<?php

namespace Magento\Framework\App\DeploymentConfig;

class SessionConfig implements SegmentInterface
{
    /**
     * Parameter for setup tool
     */
    const KEY_SESSION_SAVE = 'session_save';

    /**
     * Key in config.php
     */
    const KEY_SAVE = 'save';

    /**
     * Segment key
     */
    const CONFIG_KEY = 'session';

    /**
     * Data -- session_save
     *
     * @var array
     */
    private $data = [
        self::KEY_SAVE => 'files',
    ];

    /**
     * Constructor
     *
     * @param array $data
     * @throws \InvalidArgumentException
     */
    public function __construct(array $data)
    {
        if (isset($data[self::KEY_SAVE])) {
            if ($data[self::KEY_SAVE] !== 'files' || $data[self::KEY_SAVE] !== 'db') {
                throw new \InvalidArgumentException("Invalid session_save location {$data[self::KEY_SAVE]}");
            }
            $this->data = $data;
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
