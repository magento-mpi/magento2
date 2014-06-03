<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Cms\Controller\Adminhtml;

/**
 * Wysiwyg controller for different purposes
 *
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Wysiwyg extends \Magento\Backend\App\Action
{
    /**
     * Template directives callback
     *
     * @todo: move this to some model
     *
     * @return void
     */
    public function directiveAction()
    {
        $directive = $this->getRequest()->getParam('___directive');
        $directive = $this->_objectManager->get('Magento\Core\Helper\Data')->urlDecode($directive);
        $url = $this->_objectManager->create('Magento\Email\Model\Template\Filter')->filter($directive);
        /** @var \Magento\Framework\Image\Adapter\AdapterInterface $image */
        $image = $this->_objectManager->get('Magento\Framework\Image\AdapterFactory')->create();
        $response = $this->getResponse();
        try {
            $image->open($url);
            $response->setHeader('Content-Type', $image->getMimeType())->setBody($image->getImage());
        } catch (\Exception $e) {
            $image->open($this->_objectManager->get('Magento\Cms\Model\Wysiwyg\Config')->getSkinImagePlaceholderUrl());
            $response->setHeader('Content-Type', $image->getMimeType())->setBody($image->getImage());
            $this->_objectManager->get('Magento\Framework\Logger')->logException($e);
        }
    }
}
