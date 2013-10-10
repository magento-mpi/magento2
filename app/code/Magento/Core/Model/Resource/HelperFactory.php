<?php
/**
 * Resource helper factory
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Core\Model\Resource;

abstract class HelperFactory
{
    /**
     * @var string
     */
    protected $_moduleName;

    /**
     * @var \Magento\Core\Model\Resource\HelperPool
     */
    protected $_helperPool;

    /**
     * @param \Magento\Core\Model\Resource\HelperPool $helperPool
     */
    public function __construct(\Magento\Core\Model\Resource\HelperPool $helperPool)
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
