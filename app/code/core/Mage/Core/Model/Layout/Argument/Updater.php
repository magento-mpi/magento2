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
     * @var Mage_Core_Model_Config
     */
    protected $_objectFactory;

    /**
     * @param array $args
     * @throws InvalidArgumentException
     */
    public function __construct(array $args = array())
    {
        $this->_objectFactory = $args['objectFactory'];
        if (false === ($this->_objectFactory instanceof Mage_Core_Model_Config)) {
            throw new InvalidArgumentException('Passed wrong instance of object factory');
        }
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
            /** @var Mage_Core_Model_Layout_Argument_UpdaterInterface $updaterInstance  */
            $updaterInstance = $this->_objectFactory->getModelInstance($updater);
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
