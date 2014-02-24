<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Integration\Helper;

use Magento\Integration\Model\Integration as IntegrationModel;

class Data extends \Magento\App\Helper\AbstractHelper
{
    /**
     * Make ACL resource array compatible with jQuery jsTree component.
     *
     * @param array $resources
     * @return array
     */
    public function mapResources(array $resources)
    {
        $output = array();
        foreach ($resources as $resource) {
            $item = array();
            $item['attr']['data-id'] = $resource['id'];
            $item['data'] = $resource['title'];
            $item['children'] = array();
            if (isset($resource['children'])) {
                $item['state'] = 'open';
                $item['children'] = $this->mapResources($resource['children']);
            }
            $output[] = $item;
        }
        return $output;
    }

    /**
     * Check if integration is created using config file
     *
     * @param array $integrationData
     * @return bool true if integration is created using Config file
     */
    public function isConfigType($integrationData)
    {
        return isset($integrationData[IntegrationModel::SETUP_TYPE])
                    && $integrationData[IntegrationModel::SETUP_TYPE] == IntegrationModel::TYPE_CONFIG;
    }
}
