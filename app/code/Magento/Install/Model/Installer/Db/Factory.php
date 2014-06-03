<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Installer DB factory
 */
namespace Magento\Install\Model\Installer\Db;

class Factory
{
    /**
     * @var array
     */
    protected $_types = array('mysql4' => 'Magento\Install\Model\Installer\Db\Mysql4');

    /**
     * @var \Magento\Framework\ObjectManager
     */
    protected $_objectManager;

    /**
     * @param \Magento\Framework\ObjectManager $objectManager
     */
    public function __construct(\Magento\Framework\ObjectManager $objectManager)
    {
        $this->_objectManager = $objectManager;
    }

    /**
     * Get Installer Db type instance
     *
     * @param string $type
     * @return \Magento\Install\Model\Installer\Db\AbstractDb | bool
     * @throws \InvalidArgumentException
     */
    public function get($type)
    {
        if (!empty($type) && isset($this->_types[(string)$type])) {
            return $this->_objectManager->get($this->_types[(string)$type]);
        }
        return false;
    }
}
