<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Framework\Data\Stub;

use Magento\Framework\Data\AbstractSearchCriteriaBuilder;

class SearchCriteriaBuilder extends AbstractSearchCriteriaBuilder
{
    /**
     * @return string|void
     */
    public function init()
    {
        $this->resultObjectInterface = 'Magento\Framework\Api\CriteriaInterface';
    }
}
