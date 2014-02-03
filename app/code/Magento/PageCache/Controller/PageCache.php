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
     * @var \Magento\App\Response\Http\FileFactory
     */
    protected $_fileFactory;

    /**
     * @var \Magento\PageCache\Model\Config
     */
    protected $_pageCacheModel;

    /**
     * @param \Magento\Backend\App\Action\Context $context
     * @param \Magento\App\Response\Http\FileFactory $fileFactory
     * @param \Magento\PageCache\Model\Config $pageCacheModel
     */
    public function __construct(
        \Magento\Backend\App\Action\Context $context,
        \Magento\App\Response\Http\FileFactory $fileFactory,
        \Magento\PageCache\Model\Config $pageCacheModel
    ){
        parent::__construct($context);
        $this->_pageCacheModel = $pageCacheModel;
        $this->_fileFactory = $fileFactory;
    }

    /**
     * Export Varnish Configuration as .vcl
     *
     * @return \Magento\App\ResponseInterface
     */
    public function exportVarnishConfigAction()
    {
        $fileName = 'varnish.vcl';
        $content = $this->_pageCacheModel->getVclFile();
        return $this->_fileFactory->create($fileName, $content, \Magento\App\Filesystem::VAR_DIR);
    }
}
