<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Resource product indexer price factory
 */
namespace Magento\Catalog\Model\Resource\Product\Indexer\Price;

class Factory
{
    /**
     * Object Manager
     *
     * @var \Magento\Framework\ObjectManager
     */
    protected $_objectManager;

    /**
     * Construct
     *
     * @param \Magento\Framework\ObjectManager $objectManager
     */
    public function __construct(\Magento\Framework\ObjectManager $objectManager)
    {
        $this->_objectManager = $objectManager;
    }

    /**
     * Create indexer price
     *
     * @param string $className
     * @param array $data
     * @return \Magento\Catalog\Model\Resource\Product\Indexer\Price\DefaultPrice
     * @throws \Magento\Catalog\Exception
     */
    public function create($className, array $data = array())
    {
        $indexerPrice = $this->_objectManager->create($className, $data);

        if (!$indexerPrice instanceof \Magento\Catalog\Model\Resource\Product\Indexer\Price\DefaultPrice) {
            throw new \Magento\Catalog\Exception(
                $className . ' doesn\'t extends \Magento\Catalog\Model\Resource\Product\Indexer\Price\DefaultPrice'
            );
        }
        return $indexerPrice;
    }
}
