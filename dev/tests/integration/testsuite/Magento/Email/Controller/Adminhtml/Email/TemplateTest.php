<?php
/**
 * {license_notice}
 *
 * @copyright {copyright}
 * @license {license_link}
 */
namespace Magento\Email\Controller\Adminhtml\Email;

/**
 * @magentoAppArea adminhtml
 */
class TemplateTest extends \Magento\Backend\Utility\Controller
{
    public function testDefaultTemplateAction()
    {
        /** @var $formKey \Magento\Framework\Data\Form\FormKey */
        $formKey = $this->_objectManager->get('Magento\Framework\Data\Form\FormKey');
        $post = array(
            'form_key' => $formKey->getFormKey(),
            'code' => 'customer_password_forgot_email_template'
        );
        $this->getRequest()->setPost($post);
        $this->dispatch('backend/admin/email_template/defaultTemplate/?isAjax=true');
        $this->assertContains(
            '"template_type":2,"template_subject":"Password Reset Confirmation for {{var customer.name}}"',
            $this->getResponse()->getBody()
        );
    }
}
