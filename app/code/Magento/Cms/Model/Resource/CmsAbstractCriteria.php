<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Cms\Model\Resource;

use Magento\Framework\Data\AbstractCriteria;

/**
 * Class CmsAbstractCriteria
 */
class CmsAbstractCriteria extends AbstractCriteria
{
    /**
     * @inheritdoc
     */
    public function setFirstStoreFlag($flag = false)
    {
        $this->data['first_store_flag'] = $flag;
    }

    /**
     * @inheritdoc
     */
    public function addStoreFilter($store, $withAdmin = true)
    {
        $this->data['store_filter'] = [$store, $withAdmin];
    }
}
