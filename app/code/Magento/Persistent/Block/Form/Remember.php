<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Persistent
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Persistent\Block\Form;

/**
 * Remember Me block
 *
 * @category    Magento
 * @package     Magento_Persistent
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Remember extends \Magento\View\Element\Template
{
    /**
     * Persistent data
     *
     * @var \Magento\Persistent\Helper\Data
     */
    protected $_persistentData = null;

    /**
     * @var \Magento\Math\Random
     */
    protected $mathRandom;

    /**
     * @param \Magento\View\Element\Template\Context $context
     * @param \Magento\Persistent\Helper\Data $persistentData
     * @param \Magento\Math\Random $mathRandom
     * @param array $data
     */
    public function __construct(
        \Magento\View\Element\Template\Context $context,
        \Magento\Persistent\Helper\Data $persistentData,
        \Magento\Math\Random $mathRandom,
        array $data = array()
    ) {
        $this->_persistentData = $persistentData;
        $this->mathRandom = $mathRandom;
        parent::__construct($context, $data);
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

    /**
     * Get random string
     *
     * @param int $length
     * @param string|null $chars
     * @return string
     */
    public function getRandomString($length, $chars = null)
    {
        return $this->mathRandom->getRandomString($length, $chars);
    }
}
