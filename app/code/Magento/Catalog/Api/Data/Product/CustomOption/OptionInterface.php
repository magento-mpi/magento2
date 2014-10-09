<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Catalog\Api\Data\Product\CustomOption;

/**
 * Created from:
 * @see \Magento\Catalog\Service\V1\Product\CustomOptions\Data\Option
 * @todo Use implementation \Magento\Catalog\Model\Product\Option implements \Magento\Catalog\Api\Data\Product\CustomOption\OptionInterface
 */
interface OptionInterface
{
    /**
     * Get option id
     *
     * @return int|null
     */
    public function getOptionId();

    /**
     * Get option title
     *
     * @return string
     */
    public function getTitle();

    /**
     * Get option type
     *
     * @return string
     */
    public function getType();

    /**
     * Get sort order
     *
     * @return int
     */
    public function getSortOrder();

    /**
     * Get is require
     *
     * @return bool
     */
    public function getIsRequire();

    /**
     * Get option metadata
     *
     * @return \Magento\Catalog\Service\V1\Product\CustomOptions\Data\Option\MetadataInterface[]
     */
    public function getMetadata();
}
