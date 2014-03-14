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
     * @param \Magento\Customer\Service\V1\CustomerGroupServiceInterface $subject
     * @param string                                                     $result
     * @return string
     *
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function afterSaveGroup(\Magento\Customer\Service\V1\CustomerGroupServiceInterface $subject, $result)
    {
        $this->invalidateIndexer();
        return $result;
    }

    /**
     * @param \Magento\Customer\Service\V1\CustomerGroupServiceInterface $subject
     * @param string                                                     $result
     * @return string
     *
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function afterDeleteGroup(\Magento\Customer\Service\V1\CustomerGroupServiceInterface $subject, $result)
    {
        $this->invalidateIndexer();
        return $result;
    }
}
