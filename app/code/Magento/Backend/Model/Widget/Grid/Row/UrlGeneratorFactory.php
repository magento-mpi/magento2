<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Backend
 * @copyright   {copyright}
 * @license     {license_link}
 */


/**
 * Grid row url generator factory
 *
 * @category    Magento
 * @package     Magento_Backend
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Magento_Backend_Model_Widget_Grid_Row_UrlGeneratorFactory
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
     * Create new url generator instance
     *
     * @param string $generatorClassName
     * @param array $arguments
     * @return Magento_Backend_Model_Widget_Grid_Row_Row_Generator_UrlGenerator
     * @throws InvalidArgumentException
     */
    public function createUrlGenerator($generatorClassName, array $arguments = array())
    {
        $rowUrlGenerator = $this->_objectManager->create($generatorClassName, $arguments);
        if (false === ($rowUrlGenerator instanceof Magento_Backend_Model_Widget_Grid_Row_GeneratorInterface)) {
            throw new InvalidArgumentException('Passed wrong parameters');
        }

        return $rowUrlGenerator;
    }
}
