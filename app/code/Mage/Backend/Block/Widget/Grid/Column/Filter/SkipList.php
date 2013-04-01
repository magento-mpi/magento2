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
 * Massaction grid column filter
 *
 * @category   Mage
 * @package    Mage_Backend
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Mage_Backend_Block_Widget_Grid_Column_Filter_SkipList
    extends Mage_Backend_Block_Widget_Grid_Column_Filter_Abstract
{
    public function getCondition()
    {
        return array('nin' => $this->getValue() ?: array(0));
    }
}
