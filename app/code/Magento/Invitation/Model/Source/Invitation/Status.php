<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Invitation
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Invitation status source
 *
 * @category   Magento
 * @package    Magento_Invitation
 */
namespace Magento\Invitation\Model\Source\Invitation;

class Status
{
    /**
     * Return list of invitation statuses as options
     *
     * @return array
     */
    public function getOptions()
    {
        return array(
            \Magento\Invitation\Model\Invitation::STATUS_NEW  => __('Not Sent'),
            \Magento\Invitation\Model\Invitation::STATUS_SENT => __('Sent'),
            \Magento\Invitation\Model\Invitation::STATUS_ACCEPTED => __('Accepted'),
            \Magento\Invitation\Model\Invitation::STATUS_CANCELED => __('Discarded')
        );
    }

    /**
     * Return list of invitation statuses as options array.
     * If $useEmpty eq to true, add empty option
     *
     * @param boolean $useEmpty
     * @return array
     */
    public function toOptionsArray($useEmpty = false)
    {
        $result = array();

        if ($useEmpty) {
            $result[] = array('value' => '', 'label' => '');
        }
        foreach ($this->getOptions() as $value=>$label) {
            $result[] = array('value' => $value, 'label' => $label);
        }

        return $result;
    }

    /**
     * Return option text by value
     *
     * @param string $option
     * @return string
     */
    public function getOptionText($option)
    {
        $options = $this->getOptions();
        if (isset($options[$option])) {
            return $options[$option];
        }

        return null;
    }
}
