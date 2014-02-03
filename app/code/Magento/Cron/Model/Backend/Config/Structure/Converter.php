<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Cron
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Cron\Model\Backend\Config\Structure;

class Converter
{
    protected $groupsConfig;

    public function __construct(\Magento\Cron\Model\Groups\Config\Data $groupsConfig)
    {
        $this->groupsConfig = $groupsConfig;
    }

    public function afterConvert(array $result)
    {
        $groupIterator = 0;
        foreach ($this->groupsConfig->get() as $group => $fields) {
            $template = $result['config']['system']['sections']['system']['children']['cron']['children']['template'];
            $template['id'] = $group;
            $template['label'] .= $group;
            $template['sortOrder'] += $groupIterator++;

            $fieldIterator = 0;
            foreach ($fields as $fieldName => $value) {
                $template['children'][$fieldName]['path'] = 'system/cron/' . $group;
                $template['children'][$fieldName]['sortOrder'] += $fieldIterator++;
            }
            $result['config']['system']['sections']['system']['children']['cron']['children'][$group] = $template;
        }
        unset($result['config']['system']['sections']['system']['children']['cron']['children']['template']);
        return $result;
    }
}
