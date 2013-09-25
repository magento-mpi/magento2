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
class Magento_Backend_Model_Config_Source_Storage_Media_Database implements Magento_Core_Model_Option_ArrayInterface
{
    /**
     * @var array
     */
    protected $_connectionList;

    /**
     * @param array $connectionList
     */
    function __construct(array $connectionList)
    {
        $this->_connectionList = $connectionList;
    }

    /**
     * Options getter
     *
     * @return array
     */
    public function toOptionArray()
    {
        $connectionOptions = array();
        foreach ($this->_connectionList as $connectionName) {

            $connectionOptions[] = array('value' => $connectionName, 'label' => $connectionName);
        }
        sort($connectionOptions);
        reset($connectionOptions);
        return $connectionOptions;
    }

}
