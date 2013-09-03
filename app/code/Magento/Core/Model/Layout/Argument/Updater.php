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
 * Layout argument updater processor
 *
 * @category    Magento
 * @package     Magento_Core
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Magento_Core_Model_Layout_Argument_Updater
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
     * Apply all updater to value
     *
     * @param mixed $value
     * @param array $updaters
     * @throws InvalidArgumentException
     * @return mixed
     */
    public function applyUpdaters($value, array $updaters = array())
    {
        foreach ($updaters as $updater) {
            /** @var Magento_Core_Model_Layout_Argument_UpdaterInterface $updaterInstance */
            $updaterInstance = $this->_objectManager->create($updater, array());
            if (false === ($updaterInstance instanceof Magento_Core_Model_Layout_Argument_UpdaterInterface)) {
                throw new InvalidArgumentException($updater
                        . ' should implement Magento_Core_Model_Layout_Argument_UpdaterInterface'
                );
            }
            $value = $updaterInstance->update($value);
        }
        return $value;
    }
}
