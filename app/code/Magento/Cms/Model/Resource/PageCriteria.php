<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Cms\Model\Resource;

use Magento\Cms\Api\PageCriteriaInterface;

/**
 * Class PageCriteria
 * @package Magento\Cms\Model\Resource
 */
class PageCriteria extends CmsAbstractCriteria implements PageCriteriaInterface
{
    /**
     * @param string $mapper
     */
    public function __construct($mapper = 'Magento\Cms\Model\Resource\PageCriteriaMapper')
    {
        $this->mapperInterfaceName = $mapper;
    }
}
