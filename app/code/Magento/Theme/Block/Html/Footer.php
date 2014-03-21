<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Theme\Block\Html;

/**
 * Html page footer block
 */
class Footer extends \Magento\View\Element\Template implements \Magento\View\Block\IdentityInterface
{
    /**
     * Copyright information
     *
     * @var string
     */
    protected $_copyright;

    /**
     * @var \Magento\Customer\Model\Session
     */
    protected $_customerSession;

    /**
     * @param \Magento\View\Element\Template\Context $context
     * @param \Magento\Customer\Model\Session $customerSession
     * @param array $data
     */
    public function __construct(
        \Magento\View\Element\Template\Context $context,
        \Magento\Customer\Model\Session $customerSession,
        array $data = array()
    ) {
        $this->_customerSession = $customerSession;
        parent::__construct($context, $data);
    }

    /**
     * Set footer data
     *
     * @return void
     */
    protected function _construct()
    {
        $this->addData(array(
            'cache_lifetime'=> false,
            'cache_tags' => array(
                \Magento\Store\Model\Store::CACHE_TAG,
                \Magento\Cms\Model\Block::CACHE_TAG,
            )
        ));
    }

    /**
     * Get cache key informative items
     *
     * @return array
     */
    public function getCacheKeyInfo()
    {
        return array(
            'PAGE_FOOTER',
            $this->_storeManager->getStore()->getId(),
            (int)$this->_storeManager->getStore()->isCurrentlySecure(),
            $this->_design->getDesignTheme()->getId(),
            $this->_customerSession->isLoggedIn(),
        );
    }

    /**
     * Retrieve copyright information
     *
     * @return string
     */
    public function getCopyright()
    {
        if (!$this->_copyright) {
            $this->_copyright = $this->_storeConfig->getValue('design/footer/copyright', \Magento\Store\Model\StoreManagerInterface::SCOPE_TYPE_STORE);
        }
        return $this->_copyright;
    }

    /**
     * Return identifiers for produced content
     *
     * @return array
     */
    public function getIdentities()
    {
        return array(\Magento\Store\Model\Store::CACHE_TAG, \Magento\Cms\Model\Block::CACHE_TAG);
    }

}
