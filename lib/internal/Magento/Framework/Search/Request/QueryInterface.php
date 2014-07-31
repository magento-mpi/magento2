<?php
/**
 * Query Interface
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Framework\Search\Request;

interface QueryInterface
{
    /**
     * #@+ Query Types
     */
    const TYPE_MATCH = 'match';

    const TYPE_BOOL = 'bool';

    const TYPE_FILTER = 'filter';

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
