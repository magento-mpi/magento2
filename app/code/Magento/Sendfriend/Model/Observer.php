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
namespace Magento\Sendfriend\Model;

class Observer
{
    /**
     * @var \Magento\Sendfriend\Model\SendfriendFactory
     */
    protected $_sendfriendFactory;

    /**
     * @param \Magento\Sendfriend\Model\SendfriendFactory $sendfriendFactory
     */
    public function __construct(
        \Magento\Sendfriend\Model\SendfriendFactory $sendfriendFactory
    ) {
        $this->_sendfriendFactory = $sendfriendFactory;
    }

    /**
     * Register Sendfriend Model in global registry
     *
     * @param \Magento\Event\Observer $observer
     * @return \Magento\Sendfriend\Model\Observer
     */
    public function register(\Magento\Event\Observer $observer)
    {
        $this->_sendfriendFactory->create()->register();
        return $this;
    }
}
