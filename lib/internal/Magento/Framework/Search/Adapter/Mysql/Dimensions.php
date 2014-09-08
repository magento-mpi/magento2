<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Framework\Search\Adapter\Mysql;

use Magento\Framework\App\ScopeResolverInterface;
use Magento\Framework\DB\Adapter\AdapterInterface;
use Magento\Framework\Search\Request\Dimension;

class Dimensions
{
    const DEFAULT_DIMENSION_NAME = 'scope';

    const STORE_FIELD_NAME = 'store_id';

    /**
     * @var \Magento\Framework\App\Resource
     */
    private $resource;
    /**
     * @var ScopeResolverInterface
     */
    private $scopeResolverInterface;

    /**
     * @param \Magento\Framework\App\Resource $resource
     * @param ScopeResolverInterface $scopeResolverInterface
     */
    public function __construct(
        \Magento\Framework\App\Resource $resource,
        ScopeResolverInterface $scopeResolverInterface
    ) {
        $this->resource = $resource;
        $this->scopeResolverInterface = $scopeResolverInterface;
    }

    public function build(Dimension $dimension)
    {
        /** @var AdapterInterface $adapter */
        $adapter = $this->resource->getConnection(\Magento\Framework\App\Resource::DEFAULT_READ_RESOURCE);

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

        if (self::DEFAULT_DIMENSION_NAME === $identifier) {
            $identifier = self::STORE_FIELD_NAME;
            $value = $this->scopeResolverInterface->getScope($value . 'xx')->getId();
        }

        return sprintf(
            '%s = %s',
            $adapter->quoteIdentifier($identifier),
            $adapter->quote($value)
        );
    }
}
