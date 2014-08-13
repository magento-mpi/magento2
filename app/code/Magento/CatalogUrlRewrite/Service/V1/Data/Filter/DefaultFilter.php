<?php
/**
 * {license_notice}
 *   
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\CatalogUrlRewrite\Service\V1\Data\Filter;

use Magento\UrlRewrite\Service\V1\Data\IdentityInterface;
use Magento\UrlRewrite\Service\V1\Data\Filter;

class DefaultFilter extends Filter implements IdentityInterface
{
    /**
     * @var array
     */
    protected $data = [];

    /**
     * @param array $possibleFields
     * @param array $filterData
     */
    public function __construct(
        array $filterData = [],
        array $possibleFields = []
    ) {
        if ($filterData && $possibleFields) {
            $wrongFields = array_diff(array_keys($filterData), $possibleFields);
            if ($wrongFields) {
                throw new \InvalidArgumentException(
                    sprintf('There is wrong fields passed to filter: "%s"', implode(', ', $wrongFields))
                );
            }
            $this->data = $filterData;
        }
    }

    /**
     * {@inheritdoc}
     */
    public function getFilterType()
    {
        return $this->data['entity_type'];
    }
}
