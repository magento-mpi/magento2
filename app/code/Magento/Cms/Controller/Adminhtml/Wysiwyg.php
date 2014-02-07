<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Cms
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Cms\Controller\Adminhtml;

/**
 * Wysiwyg controller for different purposes
 *
 * @category    Magento
 * @package     Magento_Cms
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Wysiwyg extends \Magento\Backend\App\Action
{
    /**
     * Template directives callback
     *
     * TODO: move this to some model
     *
     * @return void
     */
    public function directiveAction()
    {
        $directive = $this->getRequest()->getParam('___directive');
        $directive = $this->_objectManager->get('Magento\Core\Helper\Data')->urlDecode($directive);
        $url = $this->_objectManager->create('Magento\Email\Model\Template\Filter')->filter($directive);
        /** @var \Magento\Image\Adapter\AdapterInterface $image */
        $image = $this->_objectManager->get('Magento\Image\AdapterFactory')->create();
        $response = $this->getResponse();
        try {
            $image->open($url);
            $response->setHeader('Content-Type', $image->getMimeType())->setBody($image->getImage());
        } catch (\Exception $e) {
            $image->open($this->_objectManager->get('Magento\Cms\Model\Wysiwyg\Config')->getSkinImagePlaceholderUrl());
            $response->setHeader('Content-Type', $image->getMimeType())->setBody($image->getImage());
            $this->_objectManager->get('Magento\Logger')->logException($e);
        }
    }
}
