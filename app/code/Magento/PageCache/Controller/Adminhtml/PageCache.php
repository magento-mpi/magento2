<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_PageCache
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\PageCache\Controller\Adminhtml;

/**
 * Page cache admin controller
 */
class PageCache extends \Magento\Backend\App\Action
{
    /**
     * @var \Magento\Backend\App\Response\Http\FileFactory
     */
    protected $fileFactory;

    /**
     * @var \Magento\PageCache\Model\Config
     */
    protected $config;

    /**
     * @param \Magento\Backend\App\Action\Context $context
     * @param \Magento\App\Response\Http\FileFactory $fileFactory
     * @param \Magento\PageCache\Model\Config $config
     */
    public function __construct(
        \Magento\Backend\App\Action\Context $context,
        \Magento\App\Response\Http\FileFactory $fileFactory,
        \Magento\PageCache\Model\Config $config
    ) {
        parent::__construct($context);
        $this->config = $config;
        $this->fileFactory = $fileFactory;
    }

    /**
     * Export Varnish Configuration as .vcl
     *
     * @return \Magento\App\ResponseInterface
     */
    public function exportVarnishConfigAction()
    {
        $fileName = 'varnish.vcl';
        $content = $this->config->getVclFile();
        return $this->fileFactory->create($fileName, $content, \Magento\App\Filesystem::VAR_DIR);
    }
}
