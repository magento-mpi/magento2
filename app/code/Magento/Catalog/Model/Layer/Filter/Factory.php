<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Layer filter factory
 */
namespace Magento\Catalog\Model\Layer\Filter;

class Factory
{
    /**
     * Object Manager
     *
     * @var \Magento\ObjectManager
     */
    protected $_objectManager;

    /**
     * Construct
     *
     * @param \Magento\ObjectManager $objectManager
     */
    public function __construct(\Magento\ObjectManager $objectManager)
    {
        $this->_objectManager = $objectManager;
    }

    /**
     * Create layer filter
     *
     * @param string $className
     * @param array $data
     * @return \Magento\Catalog\Model\Layer\Filter\Attribute
     * @throws \Magento\Model\Exception
     */
    public function create($className, array $data = array())
    {
        $filter = $this->_objectManager->create($className, $data);

        if (!$filter instanceof \Magento\Catalog\Model\Layer\Filter\AbstractFilter) {
            throw new \Magento\Model\Exception(
                $className . ' doesn\'t extends \Magento\Catalog\Model\Layer\Filter\AbstractFilter'
            );
        }
        return $filter;
    }
}
