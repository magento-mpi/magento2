<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Translation\Controller;

class Ajax extends \Magento\Framework\App\Action\Action
{
    /**
     * @var \Magento\Translate\Inline\ParserInterface
     */
    protected $inlineParser;

    /**
     * @param \Magento\Framework\App\Action\Context $context
     * @param \Magento\Translate\Inline\ParserInterface $inlineParser
     */
    public function __construct(
        \Magento\Framework\App\Action\Context $context,
        \Magento\Translate\Inline\ParserInterface $inlineParser
    ) {
        parent::__construct($context);

        $this->inlineParser = $inlineParser;
    }

    /**
     * Ajax action for inline translation
     *
     * @return void
     */
    public function indexAction()
    {
        $translate = (array)$this->getRequest()->getPost('translate');

        try {
            $this->inlineParser->processAjaxPost($translate);
            $response = "{success:true}";
        } catch (\Exception $e) {
            $response = "{error:true,message:'" . $e->getMessage() . "'}";
        }
        $this->getResponse()->setBody($response);

        $this->_actionFlag->set('', self::FLAG_NO_POST_DISPATCH, true);
    }
}
