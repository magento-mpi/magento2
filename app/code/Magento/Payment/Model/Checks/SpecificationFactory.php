<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Payment\Model\Checks;

/**
 * Class \Magento\Payment\Model\Methods\SpecificationFactory
 */
class SpecificationFactory
{
    /**
     * Object manager
     *
     * @var \Magento\ObjectManager
     */
    protected $objectManager;

    /** @var  array mapping */
    protected $mapping;

    /**
     * Construct
     *
     * @param \Magento\ObjectManager $objectManager
     * @param array $mapping
     */
    public function __construct(\Magento\ObjectManager $objectManager, array $mapping)
    {
        $this->objectManager = $objectManager;
        $this->mapping = $mapping;
    }

    /**
     * Creates new instances of payment method models
     *
     * @param array $data
     * @return SpecificationInterface
     * @throws \Magento\Core\Exception
     */
    public function create($data = array())
    {
        $specifications = array_intersect_key($this->mapping, array_flip($data));
        return $this->objectManager->create('Magento\Payment\Model\Checks\Composite', ['list' => $specifications]);
    }
}
