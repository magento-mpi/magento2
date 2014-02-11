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
     * @var \Magento\PageCache\Model\Version
     */
    protected $version;

    /**
     * Constructor
     *
     * @param \Magento\PageCache\Model\Version $version
     */
    public function __construct(
        \Magento\PageCache\Model\Version $version
    ) {
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
        $this->version->process();
        return $response;
    }
}
