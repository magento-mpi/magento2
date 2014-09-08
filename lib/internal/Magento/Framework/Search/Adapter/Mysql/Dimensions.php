<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Framework\Search\Adapter\Mysql;

use Magento\Framework\App\Resource;
use Magento\Framework\DB\Adapter\AdapterInterface;
use Magento\Framework\Search\Request\Dimension;
use Magento\Store\Model\StoreManagerInterface;

class Dimensions
{
    /**#@+
     * Default identifiers
     */
    const DEFAULT_DIMENSION_NAME = 'scope';

    const DEFAULT_DIMENSION_VALUE = 'default';

    /**#@-*/

    const STORE_FIELD_NAME = 'store_id';
    /**
     * @var Resource
     */
    private $resource;
    /**
     * @var StoreManagerInterface
     */
    private $storeManager;

    public function __construct(
        Resource $resource,
        StoreManagerInterface $storeManager
    ) {
        $this->resource = $resource;
        $this->storeManager = $storeManager;
    }

    public function build(Dimension $dimension)
    {
        /** @var AdapterInterface $adapter */
        $adapter = $this->resource->getConnection(Resource::DEFAULT_READ_RESOURCE);

        return $this->generateExpression($dimension, $adapter);
    }

    /**
     * @param Dimension $dimension
     * @param $adapter
     * @return string
     */
    private function generateExpression(Dimension $dimension, AdapterInterface $adapter)
    {
        $identifier = $dimension->getName();
        $value = $dimension->getValue();

        if (self::DEFAULT_DIMENSION_NAME === $identifier or self::STORE_FIELD_NAME === $identifier) {
            $identifier = $this->getDefaultIdentifier();
            if (self::DEFAULT_DIMENSION_VALUE === $value) {
                $value = $this->getDefaultValue();
            }
        }

        return sprintf(
            '%s = %s',
            $adapter->quoteIdentifier($identifier),
            $adapter->quote($value)
        );
    }

    /**
     * @return string
     */
    private function getDefaultIdentifier()
    {
        return self::STORE_FIELD_NAME;
    }

    /**
     * @return int
     */
    private function getDefaultValue()
    {
        return $this->storeManager->getStore('default')->getId();
    }
} 
