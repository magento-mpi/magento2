<?php
/**
 * {license_notice}
 *
 * @category    Mage
 * @package     Mage_Dataflow
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Convert history
 *
 * @method Mage_Dataflow_Model_Resource_Profile_History _getResource()
 * @method Mage_Dataflow_Model_Resource_Profile_History getResource()
 * @method int getProfileId()
 * @method Mage_Dataflow_Model_Profile_History setProfileId(int $value)
 * @method string getActionCode()
 * @method Mage_Dataflow_Model_Profile_History setActionCode(string $value)
 * @method int getUserId()
 * @method Mage_Dataflow_Model_Profile_History setUserId(int $value)
 * @method string getPerformedAt()
 * @method Mage_Dataflow_Model_Profile_History setPerformedAt(string $value)
 *
 * @category    Mage
 * @package     Mage_Dataflow
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Mage_Dataflow_Model_Profile_History extends Mage_Core_Model_Abstract
{
    protected function _construct()
    {
        $this->_init('Mage_Dataflow_Model_Resource_Profile_History');
    }

    protected function _beforeSave()
    {
        if (!$this->getProfileId()) {
            $profile = Mage::registry('current_convert_profile');
            if ($profile) {
                $this->setProfileId($profile->getId());
            }
        }

        if(!$this->hasData('user_id')) {
            $this->setUserId(0);
        }

        parent::_beforeSave();
        return $this;
    }
}
