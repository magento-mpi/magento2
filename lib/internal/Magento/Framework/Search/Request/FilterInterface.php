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
    const TYPE_TERM = 'term';

    const TYPE_BOOL = 'bool';

    const TYPE_RANGE = 'range';

    /**#@-*/

    /**
     * #@+ Filter Operators
     */
    const OPERATOR_EQ = 'eq';

    const OPERATOR_LTH = 'lth';

    const OPERATOR_GTH = 'gth';

    const OPERATOR_NOT = 'not';

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
