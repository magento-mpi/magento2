<?php
/**
 * {license_notice}
 *
 * @category    Mage
 * @package     Mage_Api2
 * @copyright  {copyright}
 * @license    {license_link}
 */

/**
 * API auth user
 *
 * @category    Mage
 * @package     Mage_Api2
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Mage_Api2_Model_Auth_User
{
    /**
     * Get options in "key-value" format
     *
     * @param boolean $asOptionArray OPTIONAL If TRUE - return an options array, plain array - otherwise
     * @return array
     */
    static public function getUserTypes($asOptionArray = false)
    {
        $userTypes = array();

        /** @var $helper Mage_Api2_Helper_Data */
        $helper = Mage::helper('Mage_Api2_Helper_Data');

        foreach ($helper->getUserTypes() as $modelPath) {
            /** @var $userModel Mage_Api2_Model_Auth_User_Abstract */
            $userModel = Mage::getModel($modelPath);

            if ($asOptionArray) {
                $userTypes[] = array('value' => $userModel->getType(), 'label' => $userModel->getLabel());
            } else {
                $userTypes[$userModel->getType()] = $userModel->getLabel();
            }
        }
        return $userTypes;
    }
}
