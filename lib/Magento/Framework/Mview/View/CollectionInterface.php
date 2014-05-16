<?php
/**
 * {license_notice}
 *
 * @copyright {copyright}
 * @license   {license_link}
 */
namespace Magento\Framework\Mview\View;

interface CollectionInterface
{
    /**
     * Return views by given state mode
     *
     * @param string $mode
     * @return \Magento\Framework\Mview\ViewInterface[]
     */
    public function getViewsByStateMode($mode);

    /**
     * Search all views by field value
     *
     * @param   string $column
     * @param   mixed $value
     * @return  \Magento\Framework\Mview\ViewInterface[]
     */
    public function getItemsByColumnValue($column, $value);

    /**
     * Retrieve collection views
     *
     * @return \Magento\Framework\Mview\ViewInterface[]
     */
    public function getItems();
}
