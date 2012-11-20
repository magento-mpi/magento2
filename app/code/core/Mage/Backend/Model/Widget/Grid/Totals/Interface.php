<?php
/**
 * {license_notice}
 *
 * @category    Mage
 * @package     Mage_Backend
 * @copyright   {copyright}
 * @license     {license_link}
 */

interface Mage_Backend_Model_Widget_Grid_Totals_Interface
{
    /**
     * Return object contains totals for all items in collection
     *
     * @abstract
     * @param $collection
     * @return Varien_Object
     */
    public function countTotals($collection);
}