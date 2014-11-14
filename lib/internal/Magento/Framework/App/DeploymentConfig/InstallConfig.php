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
    const KEY_DATE = 'date';

    /**
     * Segment key
     */
    const CONFIG_KEY = 'install';

    /**
     * {@inheritdoc}
     */
    public function getKey()
    {
        return self::CONFIG_KEY;
    }
}
