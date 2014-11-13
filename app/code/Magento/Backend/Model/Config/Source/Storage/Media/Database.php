<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Generate options for media database selection
 */
namespace Magento\Backend\Model\Config\Source\Storage\Media;

use Magento\Framework\App\DeploymentConfig;

class Database implements \Magento\Framework\Option\ArrayInterface
{
    /**
     * @var DeploymentConfig
     */
    protected $_deploymentConfig;

    /**
     * @param DeploymentConfig $deploymentConfig
     */
    public function __construct(DeploymentConfig $deploymentConfig)
    {
        $this->_deploymentConfig = $deploymentConfig;
    }

    /**
     * Returns list of available resources
     *
     * @return array
     */
    public function toOptionArray()
    {
        $resourceOptions = array();
        foreach (array_keys($this->_deploymentConfig->getResources()) as $resourceName) {
            $resourceOptions[] = array('value' => $resourceName, 'label' => $resourceName);
        }
        sort($resourceOptions);
        reset($resourceOptions);
        return $resourceOptions;
    }
}
