<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Catalog\Model\Indexer\Product\Price\Plugin;

class CustomerGroup extends AbstractPlugin
{
    /**
     * @param \Magento\Customer\Model\Resource\Group $subject
     * @param \Magento\Customer\Model\Resource\Group $result
     * @return \Magento\Customer\Model\Resource\Group
     *
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function afterDelete(\Magento\Customer\Model\Resource\Group $subject, $result)
    {
        $this->invalidateIndexer();
        return $result;
    }

    /**
     * @param \Magento\Customer\Model\Resource\Group $subject
     * @param \Magento\Customer\Model\Resource\Group $result
     * @return \Magento\Customer\Model\Resource\Group
     *
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function afterSave(\Magento\Customer\Model\Resource\Group $subject, $result)
    {
        $this->invalidateIndexer();
        return $result;
    }
}
