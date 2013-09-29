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
     * @var \Magento\Persistent\Helper\Data
     */
    protected $_persistentData = null;

    /**
     * @param \Magento\Persistent\Helper\Data $persistentData
     * @param \Magento\Core\Helper\Data $coreData
     * @param \Magento\Core\Block\Template\Context $context
     * @param array $data
     */
    public function __construct(
        \Magento\Persistent\Helper\Data $persistentData,
        \Magento\Core\Helper\Data $coreData,
        \Magento\Core\Block\Template\Context $context,
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
