<?php
/**
 * {license_notice}
 *
 * @category    Mage
 * @package     Mage_Core
 * @copyright   {copyright}
 * @license     {license_link}
 */


/**
 * Core Flag model
 *
 * @method Mage_Core_Model_Resource_Flag _getResource()
 * @method Mage_Core_Model_Resource_Flag getResource()
 * @method string getFlagCode()
 * @method Mage_Core_Model_Flag setFlagCode(string $value)
 * @method int getState()
 * @method Mage_Core_Model_Flag setState(int $value)
 * @method string getLastUpdate()
 * @method Mage_Core_Model_Flag setLastUpdate(string $value)
 *
 * @category    Mage
 * @package     Mage_Core
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Mage_Core_Model_Flag extends Mage_Core_Model_Abstract
{
    /**
     * Flag code
     *
     * @var string
     */
    protected $_flagCode = null;

    /**
     * Init resource model
     * Set flag_code if it is specified in arguments
     *
     */
    protected function _construct()
    {
        if ($this->hasData('flag_code')) {
            $this->_flagCode = $this->getData('flag_code');
        }
        $this->_init('Mage_Core_Model_Resource_Flag');
    }

    /**
     * Processing object before save data
     *
     * @return Mage_Core_Model_Flag
     */
    protected function _beforeSave()
    {
        if (is_null($this->_flagCode)) {
            Mage::throwException(__('Please define flag code.'));
        }

        $this->setFlagCode($this->_flagCode);
        $this->setLastUpdate(date('Y-m-d H:i:s'));

        return parent::_beforeSave();
    }

    /**
     * Retrieve flag data
     *
     * @return mixed
     */
    public function getFlagData()
    {
        if ($this->hasFlagData()) {
            return unserialize($this->getData('flag_data'));
        } else {
            return null;
        }
    }

    /**
     * Set flag data
     *
     * @param mixed $value
     * @return Mage_Core_Model_Flag
     */
    public function setFlagData($value)
    {
        return $this->setData('flag_data', serialize($value));
    }

    /**
     * load self (load by flag code)
     *
     * @return Mage_Core_Model_Flag
     */
    public function loadSelf()
    {
        if (is_null($this->_flagCode)) {
            Mage::throwException(__('Please define flag code.'));
        }

        return $this->load($this->_flagCode, 'flag_code');
    }
}
