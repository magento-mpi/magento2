<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Backend
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Generate options for media database selection
 */
namespace Magento\Backend\Model\Config\Source\Storage\Media;

class Database implements \Magento\Core\Model\Option\ArrayInterface
{
    /**
     * @var \Magento\App\Config
     */
    protected $_config;

    /**
     * @param \Magento\App\Config
     */
    public function __construct(\Magento\App\Config $config)
    {
        $this->_config = $config;
    }

    /**
     * Returns list of available resources
     *
     * @return array
     */
    public function toOptionArray()
    {
        $resourceOptions = array();
        foreach (array_keys($this->_config->getResources()) as $resourceName) {
            $resourceOptions[] = array('value' => $resourceName, 'label' => $resourceName);
        }
        sort($resourceOptions);
        reset($resourceOptions);
        return $resourceOptions;
    }
}
