<?php
/**
 * {license_notice}
 *
 * @copyright {copyright}
 * @license   {license_link}
 */

namespace Magento\Mview\View;

interface CollectionInterface
{
    /**
     * Return views by given state mode
     *
     * @param string $mode
     * @return \Magento\Mview\ViewInterface[]
     */
    public function getViewsByStateMode($mode);

    /**
     * Return views by given state status
     *
     * @param string $status
     * @return \Magento\Mview\ViewInterface[]
     */
    public function getViewsByStateStatus($status);

    /**
     * Search all views by field value
     *
     * @param   string $column
     * @param   mixed $value
     * @return  \Magento\Mview\ViewInterface[]
     */
    public function getItemsByColumnValue($column, $value);

    /**
     * Retrieve collection views
     *
     * @return \Magento\Mview\ViewInterface[]
     */
    public function getItems();
}
