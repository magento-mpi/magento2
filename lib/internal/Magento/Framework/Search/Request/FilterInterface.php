<?php
/**
 * Filter Interface
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Framework\Search\Request;

interface FilterInterface
{
    /**
     * #@+ Filter Types
     */
    const TYPE_TERM = 'termFilter';

    const TYPE_BOOL = 'boolFilter';

    const TYPE_RANGE = 'rangeFilter';

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
