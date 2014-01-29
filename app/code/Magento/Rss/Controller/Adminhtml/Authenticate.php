<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Rss
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * RSS Controller with HTTP Basic authentication
 */
namespace Magento\Rss\Controller\Adminhtml;

use Magento\Backend\App\Action;

class Authenticate extends \Magento\Backend\App\Action
{
    /**
     * @param Action\Context $context
     */
    public function __construct(Action\Context $context)
    {
        parent::__construct($context);
        $this->_objectManager->get('Magento\Backend\Model\UrlInterface')->turnOffSecretKey();
    }
}
