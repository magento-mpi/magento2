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

    const TYPE_DYNAMIC = 'dynamicBucket';

    const FIELD_VALUE = 'value';

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
     * Get Name
     *
     * @return string
     */
    public function getName();
}
