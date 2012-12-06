<?php
/**
 * {license_notice}
 *
 * @category    Mage
 * @package     Mage_Backend
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Grid column filter interface
 *
 * @category   Mage
 * @package    Mage_Backend
 * @author      Magento Core Team <core@magentocommerce.com>
 */
interface Mage_Backend_Block_Widget_Grid_Column_Filter_Interface 
{
    public function getColumn();
    public function setColumn($column);
    public function getHtml();
}
