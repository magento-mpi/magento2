<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Backend
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Grid column filter interface
 *
 * @category   Magento
 * @package    Magento_Backend
 * @author      Magento Core Team <core@magentocommerce.com>
 */
interface Magento_Backend_Block_Widget_Grid_Column_Filter_Interface
{
    public function getColumn();

    public function setColumn($column);

    public function getHtml();
}
