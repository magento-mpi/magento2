<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Adminhtml page
 *
 * @author      Magento Core Team <core@magentocommerce.com>
 */
namespace Magento\Backend\Block;

class Page extends \Magento\Backend\Block\Template
{
    /**
     * @var \Magento\Framework\Locale\ResolverInterface
     */
    protected $_localeResolver;

    /**
     * @param Template\Context $context
     * @param \Magento\Framework\Locale\ResolverInterface $localeResolver
     * @param array $data
     */
    public function __construct(
        \Magento\Backend\Block\Template\Context $context,
        \Magento\Framework\Locale\ResolverInterface $localeResolver,
        array $data = array()
    ) {
        parent::__construct($context, $data);
        $this->_localeResolver = $localeResolver;
    }

    /**
     * Class constructor
     *
     * @return void
     */
    protected function _construct()
    {
        parent::_construct();

        $this->addBodyClass($this->_request->getFullActionName('-'));
    }

    /**
     * Get current language
     *
     * @return string
     */
    public function getLang()
    {
        if (!$this->hasData('lang')) {
            $this->setData('lang', substr($this->_localeResolver->getLocaleCode(), 0, 2));
        }
        return $this->getData('lang');
    }

    /**
     * Add CSS class to page body tag
     *
     * @param string $className
     * @return $this
     */
    public function addBodyClass($className)
    {
        $className = preg_replace('#[^a-z0-9]+#', '-', strtolower($className));
        $this->setBodyClass($this->getBodyClass() . ' ' . $className);
        return $this;
    }

    /**
     * @return bool
     */
    public function isSingleStoreMode()
    {
        return $this->_storeManager->isSingleStoreMode();
    }
}
