<?php
/**
 * {license_notice}
 *
 * @copyright  {copyright}
 * @license    {license_link}
 */

namespace Magento\Framework\App\DeploymentConfig;

class SessionConfig extends Config
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
     * Constructor
     *
     * @param array $data
     * @throws \InvalidArgumentException
     */
    public function __construct(array $data)
    {
        if (isset($data[self::KEY_SAVE])) {
            if ($data[self::KEY_SAVE] !== 'files' && $data[self::KEY_SAVE] !== 'db') {
                throw new \InvalidArgumentException("Invalid session_save location {$data[self::KEY_SAVE]}");
            }
        }

        $this->data = [
            self::KEY_SAVE => 'files',
        ];

        parent::__construct($this->update($data));
    }
}
