<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Newsletter\Model\Queue;

class TransportBuilder extends \Magento\Framework\Mail\Template\TransportBuilder
{
    /**
     * Template data
     *
     * @var array
     */
    protected $templateData = array();

    /**
     * Set template data
     *
     * @param array $data
     * @return $this
     */
    public function setTemplateData($data)
    {
        $this->templateData = $data;
        return $this;
    }

    /**
     * @inheritdoc
     */
    protected function prepareMessage()
    {
        $template = $this->getTemplate()->setData($this->templateData);

        $this->message->setMessageType(
            \Magento\Framework\Mail\MessageInterface::TYPE_HTML
        )->setBody(
            $template->getProcessedTemplate()
        )->setSubject(
            $template->getSubject()
        );

        return $this;
    }
}
