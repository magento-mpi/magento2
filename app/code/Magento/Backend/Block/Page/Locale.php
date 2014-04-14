<?php
/**
 * Backend locale switcher block
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Backend\Block\Page;

class Locale extends \Magento\Backend\Block\Template
{
    /**
     * Path to template file in theme
     *
     * @var string
     */
    protected $_template = 'page/locale.phtml';

    /**
     * @var \Magento\Locale\ListsInterface
     */
    protected $_localeLists;

    /**
     * @var \Magento\Locale\ResolverInterface
     */
    protected $_localeResolver;

    /**
     * @var \Magento\Core\Helper\Url
     */
    protected $_urlHelper;

    /**
     * @param \Magento\Backend\Block\Template\Context $context
     * @param \Magento\Locale\ListsInterface $localeLists
     * @param \Magento\Locale\ResolverInterface $localeResolver
     * @param \Magento\Core\Helper\Url $urlHelper
     * @param array $data
     */
    public function __construct(
        \Magento\Backend\Block\Template\Context $context,
        \Magento\Locale\ListsInterface $localeLists,
        \Magento\Locale\ResolverInterface $localeResolver,
        \Magento\Core\Helper\Url $urlHelper,
        array $data = array()
    ) {
        $this->_localeLists = $localeLists;
        $this->_localeResolver = $localeResolver;
        $this->_urlHelper = $urlHelper;
        parent::__construct($context, $data);
    }

    /**
     * Prepare URL for change locale
     *
     * @return string
     */
    public function getChangeLocaleUrl()
    {
        return $this->getUrl('adminhtml/index/changeLocale');
    }

    /**
     * Prepare current URL for referer
     *
     * @return string
     */
    public function getUrlForReferer()
    {
        return \Magento\App\Action\Action::PARAM_NAME_URL_ENCODED . '/' . $this->_urlHelper->getEncodedUrl();
    }

    /**
     * Retrieve locale select element
     *
     * @return string
     */
    public function getLocaleSelect()
    {
        $html = $this->getLayout()->createBlock('Magento\View\Element\Html\Select')
            ->setName('locale')
            ->setId('interface_locale')
            ->setTitle(__('Interface Language'))
            ->setClass('select locale-switcher-select')
            ->setValue($this->_localeResolver->getLocale()->__toString())
            ->setOptions($this->_localeLists->getTranslatedOptionLocales())
            ->getHtml();

        return $html;
    }
}
