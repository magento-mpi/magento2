<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Product type price factory
 */
namespace Magento\Catalog\Model\Product\Type\Price;

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
     * Create price model for product of particular type
     *
     * @param string $className
     * @param array $data
     * @return \Magento\Catalog\Model\Product\Type\Price
     * @throws \Magento\Core\Exception
     */
    public function create($className, array $data = array())
    {
        $price = $this->_objectManager->create($className, $data);

        if (!$price instanceof \Magento\Catalog\Model\Product\Type\Price) {
            throw new \Magento\Core\Exception(
                $className . ' doesn\'t extends \Magento\Catalog\Model\Product\Type\Price'
            );
        }
        return $price;
    }
}
