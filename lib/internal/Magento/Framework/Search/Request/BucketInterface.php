<?php
/**
 * Aggregation Bucket Interface
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Framework\Search\Request;

interface BucketInterface
{
    /**
     * #@+ Bucket Types
     */
    const TYPE_TERM = 'term';

    const TYPE_RANGE = 'range';

    /**#@-*/

    /**
     * Get Type
     *
     * @return string
     */
    public function getType();

    /**
     * Get Name
     *
     * @return string
     */
    public function getName();
}
