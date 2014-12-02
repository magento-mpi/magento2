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
 */
class PageCriteria extends CmsAbstractCriteria implements PageCriteriaInterface
{
    /**
     * @param string $mapper
     */
    public function __construct($mapper = '')
    {
        $this->mapperInterfaceName = $mapper ?: 'Magento\Cms\Model\Resource\PageCriteriaMapper';
    }
}
