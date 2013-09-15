<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Persistent
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Remember Me block
 *
 * @category    Magento
 * @package     Magento_Persistent
 * @author      Magento Core Team <core@magentocommerce.com>
 */

namespace Magento\Persistent\Block\Form;

class Remember extends \Magento\Core\Block\Template
{
    /**
     * Persistent data
     *
     * @var Magento_Persistent_Helper_Data
     */
    protected $_persistentData = null;

    /**
     * @param Magento_Persistent_Helper_Data $persistentData
     * @param Magento_Core_Helper_Data $coreData
     * @param Magento_Core_Block_Template_Context $context
     * @param array $data
     */
    public function __construct(
        Magento_Persistent_Helper_Data $persistentData,
        Magento_Core_Helper_Data $coreData,
        Magento_Core_Block_Template_Context $context,
        array $data = array()
    ) {
        $this->_persistentData = $persistentData;
        parent::__construct($coreData, $context, $data);
    }

    /**
     * Prevent rendering if Persistent disabled
     *
     * @return string
     */
    protected function _toHtml()
    {
        return ($this->_persistentData->isEnabled() && $this->_persistentData->isRememberMeEnabled())
            ? parent::_toHtml() : '';
    }

    /**
     * Is "Remember Me" checked
     *
     * @return bool
     */
    public function isRememberMeChecked()
    {
        return $this->_persistentData->isEnabled()
            && $this->_persistentData->isRememberMeEnabled()
            && $this->_persistentData->isRememberMeCheckedDefault();
    }
}
