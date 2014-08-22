<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\SalesArchive\Service\V1\Data;

/**
 * Mapper class for \Magento\SalesArchive\Service\V1\Data\Archive
 *
 * @codeCoverageIgnore
 */
class ArchiveMapper
{
    /**
     * archiveBuilder
     *
     * @var \Magento\SalesArchive\Service\V1\Data\ArchiveBuilder
     */
    protected $archiveBuilder = null;

    /**
     * Mapper constructor
     *
     * @param \Magento\SalesArchive\Service\V1\Data\ArchiveBuilder $archiveBuilder
     */
    public function __construct(\Magento\SalesArchive\Service\V1\Data\ArchiveBuilder $archiveBuilder)
    {
        $this->archiveBuilder = $archiveBuilder;
    }

    /**
     * Return billing address
     *
     * @param \Magento\Sales\Model\Order $object
     * @return string
     */
    protected function getBillingName(\Magento\Sales\Model\Order $object)
    {
        $billingName = '';
        if ($object->getBillingAddress()) {
            $billingName = $object->getBillingAddress()->getName();
        }
        return $billingName;
    }

    /**
     * Returns shipping address
     *
     * @param \Magento\Sales\Model\Order $object
     * @return string
     */
    protected function getShippingName(\Magento\Sales\Model\Order $object)
    {
        $shippingName = '';
        if ($object->getShippingAddress()) {
            $shippingName = $object->getShippingAddress()->getName();
        }
        return $shippingName;
    }

    /**
     * Extract data object from model
     *
     * @param \Magento\Framework\Model\AbstractModel $object
     * @return \Magento\SalesArchive\Service\V1\Data\Archive
     */
    public function extractDto(\Magento\Framework\Model\AbstractModel $object)
    {
        $this->archiveBuilder->populateWithArray($object->getData());
        $this->archiveBuilder->setBillingName($this->getBillingName($object));
        $this->archiveBuilder->setShippingName($this->getShippingName($object));
        return $this->archiveBuilder->create();
    }
}
