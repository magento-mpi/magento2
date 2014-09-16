<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Catalog\Model\Indexer\Product\Price\Plugin;

use Magento\Customer\Service\V1\CustomerGroupServiceInterface;

class CustomerGroup extends AbstractPlugin
{
    /**
     * @param CustomerGroupServiceInterface $subject
     * @param string                        $result
     * @return string
     *
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function afterCreateGroup(CustomerGroupServiceInterface $subject, $result)
    {
        $this->invalidateIndexer();
        return $result;
    }

    /**
     * @param CustomerGroupServiceInterface $subject
     * @param string                        $result
     * @return string
     *
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function afterUpdateGroup(CustomerGroupServiceInterface $subject, $result)
    {
        $this->invalidateIndexer();
        return $result;
    }

    /**
     * @param CustomerGroupServiceInterface $subject
     * @param string                        $result
     * @return string
     *
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function afterDeleteGroup(CustomerGroupServiceInterface $subject, $result)
    {
        $this->invalidateIndexer();
        return $result;
    }
}
