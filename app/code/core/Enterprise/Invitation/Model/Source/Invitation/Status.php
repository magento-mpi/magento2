<?php
/**
 * Magento
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/osl-3.0.php
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@magentocommerce.com so we can send you a copy immediately.
 *
 * @category   Enterprise
 * @package    Enterprise_Invitation
 * @copyright  Copyright (c) 2008 Irubin Consulting Inc. DBA Varien (http://www.varien.com)
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
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
            Enterprise_Invitation_Model_Invitation::STATUS_SENT => Mage::helper('invitation')->__('Sent'),
            Enterprise_Invitation_Model_Invitation::STATUS_ACCEPTED => Mage::helper('invitation')->__('Accepted'),
            Enterprise_Invitation_Model_Invitation::STATUS_CANCELED => Mage::helper('invitation')->__('Canceled')
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
            $result[] = array('value' => '',
                              'label' => '');
        }

        foreach ($this->getOptions() as $value=>$label) {
            $result[] = array('value' => $value,
                              'label' => $label);
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