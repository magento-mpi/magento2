<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Install
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Installer DB factory
 */
namespace Magento\Install\Model\Installer\Db;

class Factory
{
    protected $_types = array(
        'mysql4' => 'Magento\Install\Model\Installer\Db\Mysql4'
    );

    /**
     * @var \Magento\ObjectManager
     */
    protected $_objectManager;

    /**
     * @param \Magento\ObjectManager $objectManager
     */
    public function __construct(\Magento\ObjectManager $objectManager)
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
