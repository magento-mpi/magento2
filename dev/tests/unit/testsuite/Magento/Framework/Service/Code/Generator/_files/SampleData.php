<?php
/**
 * {license_notice}
 *
 * @copyright {copyright}
 * @license   {license_link}
 */

namespace Magento\Framework\Service\Code\Generator;

use Magento\Framework\Model\AbstractExtensibleModel;

/**
 * Class SampleData
 */
class SampleData extends AbstractExtensibleModel implements
    \Magento\Framework\Service\Code\Generator\SampleDataInterface
{
    /**
     * {@inheritdoc}
     */
    public function getItems()
    {
        $this->getData('items');
    }

    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        $this->getData('name');
    }

    /**
     * {@inheritdoc}
     */
    public function getCount()
    {
        $this->getData('count');
    }

    /**
     * {@inheritdoc}
     */
    public function getCreatedAt()
    {
        $this->getData('created_at');
    }
}
