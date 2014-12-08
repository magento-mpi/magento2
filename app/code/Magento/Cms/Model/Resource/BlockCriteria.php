<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Cms\Model\Resource;

use Magento\Cms\Model\BlockCriteriaInterface;

/**
 * Class BlockCriteria
 */
class BlockCriteria extends CmsAbstractCriteria implements BlockCriteriaInterface
{
    /**
     * @param string $mapper
     */
    public function __construct($mapper = '')
    {
        $this->mapperInterfaceName = $mapper ?: 'Magento\Cms\Model\Resource\BlockCriteriaMapper';
    }
}
