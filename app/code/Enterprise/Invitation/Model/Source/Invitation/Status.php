<?php
/**
 * {license_notice}
 *
 * @category    Enterprise
 * @package     Enterprise_Invitation
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Invitation status source
 *
 * @category   Enterprise
 * @package    Enterprise_Invitation
 */
class Enterprise_Invitation_Model_Source_Invitation_Status
{
    /**
     * Return list of invitation statuses as options
     *
     * @return array
     */
    public function getOptions()
    {
        return array(
            Enterprise_Invitation_Model_Invitation::STATUS_NEW  => __('Not Sent'),
            Enterprise_Invitation_Model_Invitation::STATUS_SENT => __('Sent'),
            Enterprise_Invitation_Model_Invitation::STATUS_ACCEPTED => __('Accepted'),
            Enterprise_Invitation_Model_Invitation::STATUS_CANCELED => __('Discarded')
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
