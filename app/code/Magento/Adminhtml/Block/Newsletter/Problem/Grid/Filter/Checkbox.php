<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Adminhtml
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Adminhtml newsletter subscribers grid filter checkbox
 *
 * @category   Magento
 * @package    Magento_Adminhtml
 * @author      Magento Core Team <core@magentocommerce.com>
 */

namespace Magento\Adminhtml\Block\Newsletter\Problem\Grid\Filter;

class Checkbox extends \Magento\Adminhtml\Block\Widget\Grid\Column\Filter\AbstractFilter
{
     public function getCondition()
    {
        return array();
    }

    public function getHtml()
    {
        return '<input type="checkbox" onclick="problemController.checkCheckboxes(this)"/>';
    }
}// Class \Magento\Adminhtml\Block\Newsletter\Subscriber\Grid\Filter\Checkbox END
