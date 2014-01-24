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
     * @var \Magento\Core\Model\ConfigInterface
     */
    protected $config;

    /**
     * @var \Magento\PageCache\Model\Version
     */
    private $version;

    /**
     * Constructor
     *
     * @param \Magento\Core\Model\Layout $layout
     * @param \Magento\Core\Model\ConfigInterface $config
     * @param \Magento\PageCache\Model\Version $version
     */
    public function __construct(
        \Magento\Core\Model\Layout $layout,
        \Magento\Core\Model\ConfigInterface $config,
        \Magento\PageCache\Model\Version $version
    ){
        $this->layout = $layout;
        $this->config = $config;
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
        $maxAge = $this->config->getValue('system/headers/max-age');
        if ($this->layout->isCacheable()) {
            $response->setHeader('pragma', 'cache', true);
            if($this->layout->isPrivate()) {
                $response->setHeader('cache-control', 'private, max-age=' . $maxAge, true);
                $response->setHeader('expires',
                    gmdate('D, d M Y H:i:s T', strtotime('+' . $maxAge . ' seconds')), true);
            } else {
                $response->setHeader('cache-control', 'public, max-age=' . $maxAge, true);
                $response->setHeader('expires',
                    gmdate('D, d M Y H:i:s T', strtotime('+' . $maxAge . ' seconds')), true);
            }
        } else {
            $response->setHeader('pragma', 'no-cache', true);
            $response->setHeader('cache-control', 'no-store, no-cache, must-revalidate, max-age=0', true);
            $response->setHeader('expires',
                gmdate('D, d M Y H:i:s T', strtotime('-' . $maxAge . ' seconds')), true);
        }
        $this->version->process();
        return $response;
    }
}
