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
namespace Magento\Core\Model\Layout\Argument;

class Updater
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
     * @throws \InvalidArgumentException
     * @return mixed
     */
    public function applyUpdaters($value, array $updaters = array())
    {
        foreach ($updaters as $updater) {
            /** @var \Magento\Core\Model\Layout\Argument\UpdaterInterface $updaterInstance */
            $updaterInstance = $this->_objectManager->create($updater, array());
            if (false === ($updaterInstance instanceof \Magento\Core\Model\Layout\Argument\UpdaterInterface)) {
                throw new \InvalidArgumentException($updater
                        . ' should implement \Magento\Core\Model\Layout\Argument\UpdaterInterface'
                );
            }
            $value = $updaterInstance->update($value);
        }
        return $value;
    }
}
