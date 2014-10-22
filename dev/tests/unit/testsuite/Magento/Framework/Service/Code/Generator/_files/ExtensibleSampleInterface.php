<?php
/**
 * {license_notice}
 *
 * @copyright {copyright}
 * @license   {license_link}
 */

namespace Magento\Framework\Service\Code\Generator;

use \Magento\Framework\Api\Data\ExtensibleDataInterface;

/**
 * Interface for ExtensibleSample
 */
interface ExtensibleSampleInterface extends ExtensibleDataInterface
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

    /**
     * @return int
     */
    public function getCreatedAt();
}
