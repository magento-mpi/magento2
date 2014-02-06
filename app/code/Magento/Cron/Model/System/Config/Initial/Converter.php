<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Cron
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Cron\Model\System\Config\Initial;

class Converter
{
    /**
     * @var \Magento\Cron\Model\Groups\Config\Data
     */
    protected $groupsConfig;

    /**
     * @param \Magento\Cron\Model\Groups\Config\Data $groupsConfig
     */
    public function __construct(\Magento\Cron\Model\Groups\Config\Data $groupsConfig)
    {
        $this->groupsConfig = $groupsConfig;
    }

    /**
     * Modify global configuration for cron
     *
     * @param array $result
     * @return array
     */
    public function afterConvert(array $result)
    {
        if (isset($result['data']['default']['system'])) {
            $result['data']['default']['system']['cron'] = $this->groupsConfig->get();
        }
        return $result;
    }
}
