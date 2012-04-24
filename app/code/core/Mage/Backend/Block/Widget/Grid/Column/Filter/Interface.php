<?php
/**
 * {license_notice}
 *
 * @category    Mage
 * @package     Mage_Adminhtml
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Grid column filter interface
 *
 * @category   Mage
 * @package    Mage_Adminhtml
 * @author      Magento Core Team <core@magentocommerce.com>
 */
interface Mage_Adminhtml_Block_Widget_Grid_Column_Filter_Interface 
{
    public function getColumn();
    public function setColumn($column);
    public function getHtml();
}
