<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Framework\Search\Request;

/**
 * Aggregation Bucket Interface
 */
interface BucketInterface
{
    /**
     * #@+ Bucket Types
     */
    const TYPE_TERM = 'termBucket';

    const TYPE_RANGE = 'rangeBucket';

    /**#@-*/

    /**
     * Get Type
     *
     * @return string
     */
    public function getType();

    /**
     * Get Field
     *
     * @return string
     */
    public function getField();

    /**
     * Get Metrics
     *
     * @return array
     */
    public function getMetrics();

    /**
     * Get Name
     *
     * @return string
     */
    public function getName();
}
