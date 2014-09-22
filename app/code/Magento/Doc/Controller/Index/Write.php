<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Doc\Controller\Index;

use Magento\Doc\App\Controller\AbstractAction;
use Magento\Doc\Document\Content;
use Magento\Framework\View\TemplateEnginePool;
use Magento\Framework\App\Filesystem;


/**
 * Class Write
 * @package Magento\Doc\Controller\Index
 */
class Write extends AbstractAction
{
    /**
     * @var \Magento\Framework\View\TemplateEnginePool
     */
    protected $enginePool;

    /**
     * @var \Magento\Doc\Document\Content
     */
    protected $content;

    /**
     * @param \Magento\Framework\App\Action\Context $context
     * @param TemplateEnginePool $enginePool
     * @param Content $content
     */
    public function __construct(
        \Magento\Framework\App\Action\Context $context,
        TemplateEnginePool $enginePool,
        Content $content
    ) {
        $this->enginePool = $enginePool;
        $this->content = $content;
        parent::__construct($context);
    }

    /**
     * {@inheritdoc}
     */
    public function execute()
    {
        $action = $this->_request->getParam('action');
        $module = $this->_request->getParam('module', 'Magento_Doc');
        $name = $this->_request->getParam('name');
        $type = $this->_request->getParam('type', 'html');
        $content = $this->_request->getParam('content');
        switch ($action) {
            case 'save':
                $result = $this->content->write($content, $type, $module, $name);
                if ($result === true) {
                    $block = $this->_view->getLayout()->createBlock('Magento\Framework\View\Element\Template');
                    $templateEngine = $this->enginePool->get($type);
                    $result = $templateEngine->render($block, $content);
                } else {
                    $result = __('Document is not saved due to error.');
                }
                $this->_response->setBody($result, 'save_result');
                break;
        }
    }
}
