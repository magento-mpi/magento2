<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Catalog product option factory
 */
namespace Magento\Catalog\Model\Product\Option\Type;

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
     * Create product option
     *
     * @param string $className
     * @param array $data
     * @return \Magento\Catalog\Model\Product\Option\Type\DefaultType
     * @throws \Magento\Framework\Model\Exception
     */
    public function create($className, array $data = array())
    {
        $option = $this->_objectManager->create($className, $data);

        if (!$option instanceof \Magento\Catalog\Model\Product\Option\Type\DefaultType) {
            throw new \Magento\Framework\Model\Exception(
                $className . ' doesn\'t extends \Magento\Catalog\Model\Product\Option\Type\DefaultType'
            );
        }
        return $option;
    }
}
