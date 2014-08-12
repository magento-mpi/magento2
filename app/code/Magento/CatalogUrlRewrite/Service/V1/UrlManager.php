<?php
/**
 * {license_notice}
 *   
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\CatalogUrlRewrite\Service\V1;

use Magento\UrlRewrite\Service\V1\Data\FilterInterface;
use Magento\CatalogUrlRewrite\Service\V1\Data\FilterFactory;
use Magento\Framework\ObjectManager;
use Magento\CatalogUrlRewrite\Service\V1\UrlManager\ResolverUrlManager;

class UrlManager
{
    /**
     * @var ObjectManager
     */
    protected $objectManager;

    /**
     * @var UrlMatcherInterface
     */
    protected $manager;

    /**
     * @var ResolverUrlManager
     */
    protected $managerResolver;

    /**
     * @var FilterFactory
     */
    protected $filterFactory;

    /**
     * @param ResolverUrlManager $managerResolver
     * @param FilterFactory $filterFactory
     */
    public function __construct(ResolverUrlManager $managerResolver, FilterFactory $filterFactory)
    {
        $this->managerResolver = $managerResolver;
        $this->filterFactory = $filterFactory;
    }

    protected function getManager(FilterInterface $filter)
    {
        if (!$this->manager) {
            $this->manager =  $this->managerResolver->createUrlManager($filter);
        }
        return $this->manager;
    }

    protected function getFilter($filterData)
    {
        return $this->filterFactory->create($filterData);
    }

    public function findByFilter(array $filterData)
    {
        $filter = $this->getFilter($filterData);
        return $this->getManager($filter)->findByFilter($filter);
    }

    public function findAllByFilter(array $filterData)
    {
        $filter = $this->getFilter($filterData);
        return $this->getManager($filter)->findAllByFilter($filter);
    }
}
