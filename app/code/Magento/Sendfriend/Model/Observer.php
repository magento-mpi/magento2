<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Sendfriend
 * @copyright   {copyright}
 * @license     {license_link}
 */


/**
 * Sendfriend Observer
 *
 * @category    Magento
 * @package     Magento_Sendfriend
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Magento_Sendfriend_Model_Observer
{
    /**
     * @var Magento_Sendfriend_Model_SendfriendFactory
     */
    protected $_sendfriendFactory;

    /**
     * @param Magento_Sendfriend_Model_SendfriendFactory $sendfriendFactory
     */
    public function __construct(
        Magento_Sendfriend_Model_SendfriendFactory $sendfriendFactory
    ) {
        $this->_sendfriendFactory = $sendfriendFactory;
    }

    /**
     * Register Sendfriend Model in global registry
     *
     * @param Magento_Event_Observer $observer
     * @return Magento_Sendfriend_Model_Observer
     */
    public function register(Magento_Event_Observer $observer)
    {
        $this->_sendfriendFactory->create()->register();
        return $this;
    }
}
