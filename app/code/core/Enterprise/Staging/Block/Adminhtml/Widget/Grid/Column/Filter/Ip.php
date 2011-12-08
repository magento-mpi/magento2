<?php
/**
 * {license_notice}
 *
 * @category    Enterprise
 * @package     Enterprise_Staging
 * @copyright   {copyright}
 * @license     {license_link}
 */

class Enterprise_Staging_Block_Adminhtml_Widget_Grid_Column_Filter_Ip extends Mage_Adminhtml_Block_Widget_Grid_Column_Filter_Text
{
    /**
     * ip filter method
     */
    public function getCondition()
    {
        $ip = $this->getValue();
        return ip2long($ip);
    }
}
