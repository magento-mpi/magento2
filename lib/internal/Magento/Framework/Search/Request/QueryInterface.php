<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Framework\Search\Request;

/**
 * Query Interface
 */
interface QueryInterface
{
    /**
     * #@+ Query Types
     */
    const TYPE_MATCH = 'matchQuery';

    const TYPE_BOOL = 'boolQuery';

    const TYPE_FILTER = 'filteredQuery';

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

    /**
     * Get Boost
     *
     * @return int|null
     */
    public function getBoost();
}
