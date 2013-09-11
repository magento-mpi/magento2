<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Logging
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * User column filter for Event Log grid
 */
namespace Magento\Logging\Block\Adminhtml\Grid\Filter;

class User extends \Magento\Adminhtml\Block\Widget\Grid\Column\Filter\Select
{
    /**
     * Build filter options list
     *
     * @return array
     */
    public function _getOptions()
    {
        $options = array(array('value' => '', 'label' => __('All Users')));
        foreach (\Mage::getResourceModel('Magento\Logging\Model\Resource\Event')->getUserNames() as $username) {
            $options[] = array('value' => $username, 'label' => $username);
        }
        return $options;
    }

    /**
     * Filter condition getter
     *
     * @string
     */
    public function getCondition()
    {
        return $this->getValue();
    }
}
