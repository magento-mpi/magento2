<?php
/**
 * {license_notice}
 *
 * @category    Mage
 * @package     Mage_Backend
 * @copyright   {copyright}
 * @license     {license_link}
 */


/**
 * Grid row url generator factory
 *
 * @category    Mage
 * @package     Mage_Backend
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Mage_Backend_Model_Widget_Grid_Row_UrlGeneratorFactory
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
     * Create new url generator instance
     *
     * @param string $generatorClassName
     * @param array $arguments
     * @return Mage_Backend_Model_Widget_Grid_Row_UrlGenerator
     * @throws InvalidArgumentException
     */
    public function createUrlGenerator($generatorClassName, array $arguments = array())
    {
        $rowUrlGenerator = $this->_objectManager->create($generatorClassName, $arguments, false);
        if (false === ($rowUrlGenerator instanceof Mage_Backend_Model_Widget_Grid_Row_UrlGenerator)) {
            throw new InvalidArgumentException('Passed wrong parameters');
        }

        return $rowUrlGenerator;
    }
}
