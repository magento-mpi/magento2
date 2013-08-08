<?php
/**
 * Specification factory
 * Proxy object to Magento_Core_Model_ObjectManager
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
class Saas_Saas_Model_Limitation_Specification_Factory
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
     * @param string $name
     * @return Saas_Saas_Model_Limitation_SpecificationInterface
     * @throws InvalidArgumentException Specification must implement Saas_Saas_Model_Limitation_SpecificationInterface
     */
    public function create($name)
    {
        $specification = $this->_objectManager->get($name);
        if (!$specification instanceof Saas_Saas_Model_Limitation_SpecificationInterface) {
            throw new InvalidArgumentException(
                'Specification must implement Saas_Saas_Model_Limitation_SpecificationInterface'
            );
        }
        return $specification;
    }
}
