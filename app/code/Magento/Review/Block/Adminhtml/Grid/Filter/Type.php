<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Review
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Review\Block\Adminhtml\Grid\Filter;

/**
 * Adminhtml review grid filter by type
 *
 * @category   Magento
 * @package    Magento_Review
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Type extends \Magento\Backend\Block\Widget\Grid\Column\Filter\Select
{
    /**
     * Get grid options
     *
     * @return array
     */
    protected function _getOptions()
    {
        return array(
              array('label'=>'', 'value'=>''),
              array('label'=>__('Administrator'), 'value'=>1),
              array('label'=>__('Customer'), 'value'=>2),
              array('label'=>__('Guest'), 'value'=>3)
        );
    }

    /**
     * Get condition
     *
     * @return int
     */
    public function getCondition()
    {
        if ($this->getValue() == 1) {
            return 1;
        } elseif ($this->getValue() == 2) {
            return 2;
        } else {
            return 3;
        }
    }
}// Class \Magento\Review\Block\Adminhtml\Grid\Filter\Type END
