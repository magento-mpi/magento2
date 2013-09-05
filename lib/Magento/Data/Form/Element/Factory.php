<?php
/**
 * {license_notice}
 *
 * @category   Magento
 * @package    Magento_Data
 * @copyright  {copyright}
 * @license    {license_link}
 */


/**
 * @category   Magento
 * @package    Magento_Data
 * @author     Magento Core Team <core@magentocommerce.com>
 */
class Magento_Data_Form_Element_Factory
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
     * Factory method
     *
     * @param string $elementType
     * @param array $config
     * @return Magento_Data_Form_Element_Abstract
     * @throws InvalidArgumentException
     */
    public function create($elementType, array $config = array())
    {
        $instanceName = 'Magento_Data_Form_Element_' . ucfirst($elementType);
        if (preg_match('/_/', $elementType)) {
            $instanceName = $elementType;
        }

        $instance = $this->_objectManager->create($instanceName, $config);
        if (!($instance instanceof Magento_Data_Form_Element_Abstract)) {
            throw new InvalidArgumentException($elementType . ' doesn\'n extended Magento_Data_Form_Element_Abstract');
        }
        return $instance;
    }
}
