<?php
/**
 * {license_notice}
 *   
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\CatalogUrlRewrite\Service\V1;

use Magento\UrlRewrite\Service\V1\UrlMatcherInterface;
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

    /**
     * @param FilterInterface $filter
     * @return UrlMatcherInterface|mixed
     * @throws \Exception
     */
    protected function getManager(FilterInterface $filter)
    {
        if (!$this->manager) {
            $this->manager =  $this->managerResolver->createUrlManager($filter);
        }
        return $this->manager;
    }

    /**
     * @param $filterData
     * @return FilterInterface
     * @throws \Exception
     */
    protected function getFilter($filterData)
    {
        return $this->filterFactory->create($filterData);
    }

    /**
     * @param array $filterData
     * @return \Magento\UrlRewrite\Service\V1\Data\UrlRewrite|null
     */
    public function findByFilter(array $filterData)
    {
        $filter = $this->getFilter($filterData);
        return $this->getManager($filter)->findByFilter($filter);
    }

    /**
     * @param array $filterData
     * @return \Magento\UrlRewrite\Service\V1\Data\UrlRewrite[]
     */
    public function findAllByFilter(array $filterData)
    {
        $filter = $this->getFilter($filterData);
        return $this->getManager($filter)->findAllByFilter($filter);
    }
}
