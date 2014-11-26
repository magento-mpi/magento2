<?php
/**
 * {license_notice}
 *
 * @copyright  {copyright}
 * @license    {license_link}
 */

namespace Magento\Framework\App\DeploymentConfig;

class InstallConfig extends AbstractSegment
{
    /**
     * Array Key for install date
     */
    const KEY_DATE = 'date';

    /**
     * Segment key
     */
    const CONFIG_KEY = 'install';

    /**
     * Constructor
     *
     * @param array $data
     * @throws \InvalidArgumentException
     */
    public function __construct(array $data)
    {
        if (!isset($data[self::KEY_DATE])) {
            throw new \InvalidArgumentException('Install date not provided');
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
