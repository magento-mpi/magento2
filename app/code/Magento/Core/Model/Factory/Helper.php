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
namespace Magento\Core\Model\Factory;

class Helper
{
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
     * Get helper singleton
     *
     * @param string $className
     * @param array $arguments
     * @return \Magento\Core\Helper\AbstractHelper
     * @throws \LogicException
     */
    public function get($className, array $arguments = array())
    {
        $className = str_replace('_', '\\', $className);
        /* Default helper class for a module */
        if (strpos($className, '\Helper\\') === false) {
            $className .= '\Helper\Data';
        }

        $helper = $this->_objectManager->get($className, $arguments);

        if (false === ($helper instanceof \Magento\Core\Helper\AbstractHelper)) {
            throw new \LogicException(
                $className . ' doesn\'t extends Magento\Core\Helper\AbstractHelper'
            );
        }

        return $helper;
    }
}
