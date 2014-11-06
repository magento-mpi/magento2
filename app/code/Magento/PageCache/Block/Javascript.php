<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\PageCache\Block;

/**
 * Class Form
 */
class Javascript extends \Magento\Framework\View\Element\Template
{
    /**
     * @var \Magento\PageCache\Helper\Data
     */
    protected $helper;

    /**
     * Constructor
     *
     * @param \Magento\Framework\View\Element\Template\Context $context
     * @param \Magento\PageCache\Helper\Data         $helper
     * @param array                                  $data
     */
    public function __construct(
        \Magento\Framework\View\Element\Template\Context $context,
        \Magento\PageCache\Helper\Data $helper,
        array $data = array()
    ) {
        $this->helper = $helper;
        parent::__construct($context, $data);
    }

    /**
     * Retrieve script options encoded to json
     *
     * @return string
     */
    public function getScriptOptions()
    {
        $params = array(
            'url' => $this->getUrl('page_cache/block/render/', array('_current' => true)),
            'handles' => $this->helper->getActualHandles(),
            'versionCookieName' => \Magento\Framework\App\PageCache\Version::COOKIE_NAME
        );
        return json_encode($params);
    }
}
