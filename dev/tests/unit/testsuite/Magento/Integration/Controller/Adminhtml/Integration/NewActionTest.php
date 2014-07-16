<?php
/**
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Integration\Controller\Adminhtml\Integration;

class NewActionTest extends \Magento\Integration\Controller\Adminhtml\IntegrationTest
{
    public function testNewAction()
    {
        $this->_verifyLoadAndRenderLayout();
        // verify the request is forwarded to 'edit' action
        $this->_requestMock->expects(
            $this->any()
        )->method(
                'setActionName'
            )->with(
                'edit'
            )->will(
                $this->returnValue($this->_requestMock)
            );
        $integrationContr = $this->_createIntegrationController('NewAction');
        $integrationContr->execute();
    }
}
