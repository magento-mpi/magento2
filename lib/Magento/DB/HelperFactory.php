<?php
/**
 * DB helper factory
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\DB;

abstract class HelperFactory
{
    /**
     * @var string
     */
    protected $_moduleName;

    /**
     * @var \Magento\DB\HelperPool
     */
    protected $_helperPool;

    /**
     * @param \Magento\DB\HelperPool $helperPool
     */
    public function __construct(\Magento\DB\HelperPool $helperPool)
    {
        $this->_helperPool = $helperPool;
    }

    /**
     * Create resource helper instance
     *
     * @return void
     */
    public function create()
    {
        return $this->_helperPool->get($this->_moduleName);
    }
}
