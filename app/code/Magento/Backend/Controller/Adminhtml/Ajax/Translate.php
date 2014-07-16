<?php
/**
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Backend\Controller\Adminhtml\Ajax;

use Magento\Backend\App\Action;

class Translate extends \Magento\Backend\App\Action
{
    /**
     * @var \Magento\Framework\Translate\Inline\ParserInterface
     */
    protected $inlineParser;

    /**
     * @param Action\Context $context
     * @param \Magento\Framework\Translate\Inline\ParserInterface $inlineParser
     */
    public function __construct(
        Action\Context $context,
        \Magento\Framework\Translate\Inline\ParserInterface $inlineParser
    ) {
        parent::__construct($context);

        $this->inlineParser = $inlineParser;
    }

    /**
     * Ajax action for inline translation
     *
     * @return void
     */
    public function execute()
    {
        $translate = (array)$this->getRequest()->getPost('translate');

        try {
            $this->inlineParser->processAjaxPost($translate);
            $response = "{success:true}";
        } catch (\Exception $e) {
            $response = "{error:true,message:'" . $e->getMessage() . "'}";
        }
        $this->getResponse()->representJson($response);

        $this->_actionFlag->set('', self::FLAG_NO_POST_DISPATCH, true);
    }
}
