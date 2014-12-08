<?php
/**
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Integration\Controller\Adminhtml\Integration;

class IndexTest extends \Magento\Integration\Controller\Adminhtml\IntegrationTest
{
    public function testIndexAction()
    {
        $this->_verifyLoadAndRenderLayout();
        // renderLayout
        $this->_controller = $this->_createIntegrationController('Index');
        $this->_controller->execute();
    }
}
