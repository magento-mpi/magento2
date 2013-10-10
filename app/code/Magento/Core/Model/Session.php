<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Core
 * @copyright   {copyright}
 * @license     {license_link}
 */


/**
 * Core session model
 *
 * @todo extend from \Magento\Core\Model\Session\AbstractSession
 *
 * @method null|bool getCookieShouldBeReceived()
 * @method \Magento\Core\Model\Session setCookieShouldBeReceived(bool $flag)
 * @method \Magento\Core\Model\Session unsCookieShouldBeReceived()
 */
namespace Magento\Core\Model;

class Session extends \Magento\Core\Model\Session\AbstractSession
{
    /**
     * Core data
     *
     * @var \Magento\Core\Helper\Data
     */
    protected $_coreData;

    /**
     * @param \Magento\Core\Model\Session\Context $context
     * @param \Magento\Core\Helper\Data $coreData
     * @param array $data
     * @param string|null $sessionName
     */
    public function __construct(
        \Magento\Core\Model\Session\Context $context,
        \Magento\Core\Helper\Data $coreData,
        array $data = array(),
        $sessionName = null
    ) {
        $this->_coreData = $coreData;
        parent::__construct($context, $data);
        $this->init('core', $sessionName);
    }

    /**
     * Retrieve Session Form Key
     *
     * @return string A 16 bit unique key for forms
     */
    public function getFormKey()
    {
        if (!$this->getData('_form_key')) {
            $this->setData('_form_key', $this->_coreData->getRandomString(16));
        }
        return $this->getData('_form_key');
    }
}
