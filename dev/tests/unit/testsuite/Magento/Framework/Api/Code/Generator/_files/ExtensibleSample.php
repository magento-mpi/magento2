<?php
/**
 * @copyright Copyright (c) 2014 X.commerce, Inc. (http://www.magentocommerce.com)
 */

namespace Magento\Framework\Api\Code\Generator;

use Magento\Framework\Model\AbstractExtensibleModel;

/**
 * Class ExtensibleSample
 */
class ExtensibleSample extends AbstractExtensibleModel implements
    \Magento\Framework\Api\Code\Generator\ExtensibleSampleInterface
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
