<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Product type factory
 */
namespace Magento\Catalog\Model\Product\Type;

class Pool
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
     * Gets product of particular type
     *
     * @param string $className
     * @param array $data
     * @return \Magento\Catalog\Model\Product\Type\AbstractType
     * @throws \Magento\Core\Exception
     */
    public function get($className, array $data = array())
    {
        $product = $this->_objectManager->get($className, $data);

        if (!$product instanceof \Magento\Catalog\Model\Product\Type\AbstractType) {
            throw new \Magento\Core\Exception($className
                . ' doesn\'t extends \Magento\Catalog\Model\Product\Type\AbstractType');
        }
        return $product;
    }
}
