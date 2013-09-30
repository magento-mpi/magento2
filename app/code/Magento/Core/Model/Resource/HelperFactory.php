<?php
/**
 * Resource helper factory
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

abstract class Magento_Core_Model_Resource_HelperFactory
{
    /**
     * @var string
     */
    protected $_moduleName;

    /**
     * @var Magento_Core_Model_Resource_HelperPool
     */
    protected $_helperPool;

    /**
     * @param Magento_Core_Model_Resource_HelperPool $helperPool
     */
    public function __construct(Magento_Core_Model_Resource_HelperPool $helperPool)
    {
        $this->_helperPool = $helperPool;
    }

    /**
     * Create resource helper instance
     *
     */
    public function create()
    {
        return $this->_helperPool->get($this->_moduleName);
    }
}
