<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\PageCache\Model\App\FrontController;

/**
 * Class HeadPlugin
 */
class HeaderPlugin
{
    /**
     * @var \Magento\Core\Model\Layout
     */
    protected $layout;

    /**
     * @var \Magento\PageCache\Helper\Data
     */
    protected $helper;

    /**
     * @var \Magento\PageCache\Model\Version
     */
    private $version;

    /**
     * Constructor
     *
     * @param \Magento\Core\Model\Layout $layout
     * @param \Magento\PageCache\Helper\Data $helper
     * @param \Magento\PageCache\Model\Version $version
     */
    public function __construct(
        \Magento\Core\Model\Layout $layout,
        \Magento\PageCache\Helper\Data $helper,
        \Magento\PageCache\Model\Version $version
    ){
        $this->layout = $layout;
        $this->helper = $helper;
        $this->version = $version;
    }

    /**
     * Modify response after dispatch
     *
     * @param \Magento\App\Response\Http $response
     * @return \Magento\App\Response\Http
     */
    public function afterDispatch(\Magento\App\Response\Http $response)
    {
        if ($this->layout->isCacheable()) {
            $response->setHeader('pragma', 'cache', true);
            if(!$response->getHeader('cache-control')) {
                $this->setPublicHeaders($response);
            }
        } else {
            $this->setNocacheHeaders($response);
        }
        $this->version->process();
        return $response;
    }

    /**
     * @param \Magento\App\Response\Http $response
     */
    protected function setPublicHeaders(\Magento\App\Response\Http $response)
    {
        $maxAge = $this->helper->getMaxAgeCache();
        $response->setHeader('cache-control', 'public, max-age=' . $maxAge, true);
        $response->setHeader(
            'expires',
            gmdate('D, d M Y H:i:s T', strtotime('+' . $maxAge . ' seconds')),
            true
        );
    }

    /**
     * @param \Magento\App\Response\Http $response
     */
    protected function setNocacheHeaders(\Magento\App\Response\Http $response)
    {
        $maxAge = $this->helper->getMaxAgeCache();
        $response->setHeader('pragma', 'no-cache', true);
        $response->setHeader('cache-control', 'no-store, no-cache, must-revalidate, max-age=0', true);
        $response->setHeader(
            'expires',
            gmdate('D, d M Y H:i:s T', strtotime('-' . $maxAge . ' seconds')),
            true
        );
    }
}
