<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Validator
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Presentation layer validator class.
 */
class Magento_Validator
{
    /** @var string */
    protected $_entityName;
    /** @var string */
    protected $_groupName;
    /** @var Magento_Config_Validation */
    protected $_config;
    /** @var array */
    protected $_messages;

    /**
     * Set validation entity and group names, load validator config.
     *
     * @param string $entityName
     * @param string $groupName
     * @throws InvalidArgumentException
     */
    public function __construct($entityName, $groupName)
    {
        if (!$entityName) {
            throw new InvalidArgumentException('Validation entity name is required.');
        }

        if (!$groupName) {
            throw new InvalidArgumentException('Validation group name is required.');
        }

        $configFiles = glob(Mage::getBaseDir('app') . "code/*/*/*/etc/validation.xml", GLOB_NOSORT);
        $this->_config = new Magento_Config_Validation($configFiles);
    }
}