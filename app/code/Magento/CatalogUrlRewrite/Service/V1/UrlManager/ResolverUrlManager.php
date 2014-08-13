<?php
/**
 * {license_notice}
 *   
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\CatalogUrlRewrite\Service\V1\UrlManager;

use Magento\Framework\ObjectManager;
use Magento\UrlRewrite\Service\V1\Data\IdentityInterface;
use Magento\UrlRewrite\Service\V1\UrlMatcherInterface;

class ResolverUrlManager
{
    /**
     * @var ObjectManager
     */
    protected $objectManager;

    /**
     * @var array
     */
    protected $managerClasses;

    /**
     * @param ObjectManager $objectManager
     * @param array $managerClasses
     */
    public function __construct(ObjectManager $objectManager, $managerClasses = [])
    {
        $this->objectManager = $objectManager;
        $this->managerClasses = $managerClasses;
    }

    /**
     * @param IdentityInterface $filter
     * @return mixed
     * @throws \Exception
     */
    public function createUrlManager(IdentityInterface $filter)
    {
        if (empty($this->managerClasses[$filter->getFilterType()])) {
            throw new \Exception('Undefined manager class');
        }

        $managerObject = $this->objectManager->create(
            $this->managerClasses[$filter->getFilterType()]
        );

        if (!$managerObject instanceof UrlMatcherInterface) {
            throw new \Exception('Invalidate manager object');
        }

        return $managerObject;
    }
}
