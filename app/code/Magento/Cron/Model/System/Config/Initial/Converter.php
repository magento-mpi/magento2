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
    protected $groupsConfig;

    public function __construct(\Magento\Cron\Model\Groups\Config\Data $groupsConfig)
    {
        $this->groupsConfig = $groupsConfig;
    }

    public function afterConvert(array $result)
    {
        $result['data']['default']['system']['cron'] = $this->groupsConfig->get();
        return $result;
    }
}
