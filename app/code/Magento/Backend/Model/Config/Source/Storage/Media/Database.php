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
     * @var \Magento\Core\Model\ConfigInterface
     */
    protected $_config;

    /**
     * @param \Magento\Core\Model\Config\Local $config
     */
    public function __construct(\Magento\Core\Model\Config\Local $config)
    {
        $this->_config = $config;
    }
    
    /**
     * Options getter
     *
     * @return array
     */
    public function toOptionArray()
    {
        $connectionOptions = array();
        foreach (array_keys($this->_config->getConnections()) as $connectionName) {
            $connectionOptions[] = array('value' => $connectionName, 'label' => $connectionName);
        }
        sort($connectionOptions);
        reset($connectionOptions);
        return $connectionOptions;
    }
}
