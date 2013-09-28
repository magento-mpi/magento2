<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_TargetRule
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Resource helper pool
 */
namespace Magento\Core\Model\Resource;

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
     * @return \Magento\Core\Model\Resource\Helper\AbstractHelper
     */
    public function get($moduleName)
    {
        $helperClassName = $moduleName . '\Model\Resource\Helper';
        $connection = strtolower($moduleName);
        if (substr($moduleName, 0, 8) == 'Magento_') {
            $connection = substr($connection, 8);
        }

        if (!isset($this->_resourceHelpers[$connection])) {
            $helper = $this->_objectManager->create($helperClassName, array('modulePrefix' => $connection));
            if (false === ($helper instanceof \Magento\Core\Model\Resource\Helper\AbstractHelper)) {
                throw new \InvalidArgumentException(
                    $helperClassName . ' doesn\'t extend \Magento\Core\Model\Resource\Helper\AbstractHelper'
                );
            }
            $this->_resourceHelpers[$connection] = $helper;
        }

        return $this->_resourceHelpers[$connection];
    }
}
