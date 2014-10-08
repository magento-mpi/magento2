<?php
/**
 * {license_notice}
 *
 * @copyright {copyright}
 * @license   {license_link}
 */

namespace Magento\Framework\Service\Code\Generator;

use Magento\Framework\Model\AbstractModel;

/**
 * Class Sample for Proxy and Factory generation
 */
class SampleData extends AbstractModel implements SampleDataInterface
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
}
