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
class Footer extends \Magento\Framework\View\Element\Template implements \Magento\Framework\View\Block\IdentityInterface
{
    /**
     * Copyright information
     *
     * @var string
     */
    protected $_copyright;

    /**
     * @var \Magento\Framework\App\Http\Context
     */
    protected $httpContext;

    /**
     * @param \Magento\Framework\View\Element\Template\Context $context
     * @param \Magento\Framework\App\Http\Context $httpContext
     * @param array $data
     */
    public function __construct(
        \Magento\Framework\View\Element\Template\Context $context,
        \Magento\Framework\App\Http\Context $httpContext,
        array $data = array()
    ) {
        $this->httpContext = $httpContext;
        parent::__construct($context, $data);
    }

    /**
     * Set footer data
     *
     * @return void
     */
    protected function _construct()
    {
        $this->addData(
            array(
                'cache_lifetime' => false,
                'cache_tags' => array(\Magento\Store\Model\Store::CACHE_TAG, \Magento\Cms\Model\Block::CACHE_TAG)
            )
        );
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
            $this->httpContext->getValue(\Magento\Customer\Helper\Data::CONTEXT_AUTH),
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
            $this->_copyright = $this->_scopeConfig->getValue(
                'design/footer/copyright',
                \Magento\Store\Model\ScopeInterface::SCOPE_STORE
            );
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
