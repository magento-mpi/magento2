<?php
/**
 * {license_notice}
 *
 * @copyright {copyright}
 * @license   {license_link}
 */

namespace Magento\Framework\Service\Code\Generator;

use Magento\Framework\Service\Data\ExtensibleEntityInterface;

/**
 * Interface for SampleData
 */
interface SampleDataInterface extends ExtensibleEntityInterface
{
    /**
     * @return array
     */
    public function getItems();

    /**
     * @return string
     */
    public function getName();

    /**
     * @return int
     */
    public function getCount();
}
