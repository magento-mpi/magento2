<?php
/**
 * DB helper pool
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\DB;

class HelperPool
{
    /**
     * @var \Magento\ObjectManager
     */
    protected $_objectManager;

    /**
     * @var array
     */
    protected $_resourceHelpers = array();

    /**
     * @param \Magento\ObjectManager $objectManager
     */
    public function __construct(
        \Magento\ObjectManager $objectManager
    ) {
        $this->_objectManager = $objectManager;
    }

    /**
     * Get resource helper singleton
     *
     * @param string $moduleName
     * @throws \InvalidArgumentException
     * @return \Magento\DB\Helper\AbstractHelper
     */
    public function get($moduleName)
    {
        $module = str_replace('_', \Magento\Autoload\IncludePath::NS_SEPARATOR, $moduleName);
        $helperClassName = $module . '\Model\Resource\Helper';
        $connection = strtolower($moduleName);
        if (substr($moduleName, 0, 8) == 'Magento_') {
            $connection = substr($connection, 8);
        }

        if (!isset($this->_resourceHelpers[$connection])) {
            $helper = $this->_objectManager->create($helperClassName, array('modulePrefix' => $connection));
            if (false === ($helper instanceof \Magento\DB\Helper\AbstractHelper)) {
                throw new \InvalidArgumentException(
                    $helperClassName . ' doesn\'t extend \Magento\DB\Helper\AbstractHelper'
                );
            }
            $this->_resourceHelpers[$connection] = $helper;
        }

        return $this->_resourceHelpers[$connection];
    }
}
