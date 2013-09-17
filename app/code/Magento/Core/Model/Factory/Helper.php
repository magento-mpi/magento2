<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Core
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Helper factory model. Used to get helper objects
 */
class Magento_Core_Model_Factory_Helper
{
    /**
     * @var Magento_ObjectManager
     */
    protected $_objectManager;

    /**
     * @param Magento_ObjectManager $objectManager
     */
    public function __construct(Magento_ObjectManager $objectManager)
    {
        $this->_objectManager = $objectManager;
    }

    /**
     * Get helper singleton
     *
     * @param string $className
     * @param array $arguments
     * @return Magento_Core_Helper_Abstract
     * @throws LogicException
     */
    public function get($className, array $arguments = array())
    {
        /* Default helper class for a module */
        if (strpos($className, '_Helper_') === false) {
            $className .= '_Helper_Data';
        }

        $helper = $this->_objectManager->get($className, $arguments);

        if (false === ($helper instanceof Magento_Core_Helper_Abstract)) {
            throw new LogicException(
                $className . ' doesn\'t extends Magento_Core_Helper_Abstract'
            );
        }

        return $helper;
    }
}
