<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Catalog\Service\V1\Product\Link\Data\ProductLink\DataMapper;

use \Magento\Catalog\Service\V1\Product\Link\Data\ProductLink\DataMapperInterface;

class Composite implements DataMapperInterface
{
    /**
     * @var DataMapperInterface[]
     */
    protected $mappers;

    /**
     * @param DataMapperInterface[] $mappers
     */
    public function __construct(array $mappers = array())
    {
        $this->mappers = $mappers;
    }

    /**
     * {@inheritdoc}
     */
    public function map(array $data)
    {
        foreach ($this->mappers as $mapper) {
            $data = $mapper->map($data);
        }
        return $data;
    }
}
