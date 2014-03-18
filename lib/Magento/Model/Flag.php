<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Core
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Model;

use Magento\Model\AbstractModel;

/**
 * Flag model
 *
 * @method \\Magento\Model\Resource\Flag _getResource()
 * @method \\Magento\Model\Resource\Flag getResource()
 * @method string getFlagCode()
 * @method \Magento\Model\Flag setFlagCode(string $value)
 * @method int getState()
 * @method \Magento\Model\Flag setState(int $value)
 * @method string getLastUpdate()
 * @method \Magento\Model\Flag setLastUpdate(string $value)
 */
class Flag extends AbstractModel
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
     * @return void
     */
    protected function _construct()
    {
        if ($this->hasData('flag_code')) {
            $this->_flagCode = $this->getData('flag_code');
        }
        $this->_init('\Magento\Model\Resource\Flag');
    }

    /**
     * Processing object before save data
     *
     * @throws \Magento\Model\Exception
     * @return $this
     */
    protected function _beforeSave()
    {
        if (is_null($this->_flagCode)) {
            throw new \Magento\Model\Exception(__('Please define flag code.'));
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
     * @return $this
     */
    public function setFlagData($value)
    {
        return $this->setData('flag_data', serialize($value));
    }

    /**
     * load self (load by flag code)
     *
     * @throws \Magento\Model\Exception
     * @return $this
     */
    public function loadSelf()
    {
        if (is_null($this->_flagCode)) {
            throw new \Magento\Model\Exception(__('Please define flag code.'));
        }

        return $this->load($this->_flagCode, 'flag_code');
    }
}
