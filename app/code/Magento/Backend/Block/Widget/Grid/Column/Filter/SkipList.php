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
 * Massaction grid column filter
 *
 * @category   Magento
 * @package    Magento_Backend
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Magento_Backend_Block_Widget_Grid_Column_Filter_SkipList
    extends Magento_Backend_Block_Widget_Grid_Column_Filter_Abstract
{
    public function getCondition()
    {
        return array('nin' => $this->getValue() ?: array(0));
    }
}
