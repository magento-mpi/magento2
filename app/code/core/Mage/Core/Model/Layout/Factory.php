<?php
/**
 * {license_notice}
 *
 * @category    Mage
 * @package     Mage_Core
 * @copyright   {copyright}
 * @license     {license_link}
 */

class Mage_Core_Model_Layout_Factory
{
    const CLASS_NAME = 'Mage_Core_Model_Layout';

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
     * @param array $arguments
     * @param string $className
     * @return Mage_Core_Model_Layout
     */
    public function createLayout(array $arguments = array(), $className = self::CLASS_NAME)
    {
        // because layout singleton was used everywhere in magento code, in observers, models, blocks, etc.
        // the only way how we can replace default layout object with custom one is to save instance of custom layout
        // to instance manager storage using default layout class name as alias
        $createLayout = true;

        if ($this->_objectManager->hasSharedInstance(self::CLASS_NAME)) {
            /** @var $layout Mage_Core_Model_Layout */
            $layout = $this->_objectManager->get(self::CLASS_NAME);
            if ((isset($arguments['area']) && $arguments['area'] != $layout->getArea())
                || $className != get_class($layout)
            ) {
                $this->_objectManager->removeSharedInstance(self::CLASS_NAME);
            } else {
                $createLayout = false;
            }
        }
        if ($createLayout) {
            $layout = $this->_objectManager->create($className, $arguments, false);
            $this->_objectManager->addSharedInstance($layout, self::CLASS_NAME);
        }

        return $this->_objectManager->get(self::CLASS_NAME);
    }
}
