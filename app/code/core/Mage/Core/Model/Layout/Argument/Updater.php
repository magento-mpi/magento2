<?php
/**
 * {license_notice}
 *
 * @category    Mage
 * @package     Mage_Core
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Layout argument updater processor
 *
 * @category    Mage
 * @package     Mage_Core
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Mage_Core_Model_Layout_Argument_Updater
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
            /** @var Mage_Core_Model_Layout_Argument_UpdaterInterface $updaterInstance */
            $updaterInstance = $this->_objectManager->create($updater, array(), false);
            if (false === ($updaterInstance instanceof Mage_Core_Model_Layout_Argument_UpdaterInterface)) {
                throw new InvalidArgumentException($updater
                        . ' should implement Mage_Core_Model_Layout_Argument_UpdaterInterface'
                );
            }
            $value = $updaterInstance->update($value);
        }
        return $value;
    }
}
