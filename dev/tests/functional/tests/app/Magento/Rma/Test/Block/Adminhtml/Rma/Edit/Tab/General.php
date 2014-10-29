<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Rma\Test\Block\Adminhtml\Rma\Edit\Tab;

use Mtf\Client\Element;
use Mtf\Client\Element\Locator;

/**
 * General information tab on rma edit page(backend).
 */
class General extends \Magento\Backend\Test\Block\Widget\Tab
{
    /**
     * Mapping for request details fields.
     *
     * @var array
     */
    protected $requestDetails = [
        'entity_id' => [
            'selector' => './/*[@class="rma-request-details"]//tr[1]/td[1]',
            'strategy' => Locator::SELECTOR_XPATH
        ],
        'order_id' => [
            'selector' => './/*[@class="rma-request-details"]//tr[2]/td[1]',
            'strategy' => Locator::SELECTOR_XPATH
        ],
        'customer_name' => [
            'selector' => './/*[@class="rma-request-details"]//tr[3]/td[1]',
            'strategy' => Locator::SELECTOR_XPATH
        ],
        'customer_email' => [
            'selector' => './/*[@class="rma-request-details"]//tr[4]/td[1]',
            'strategy' => Locator::SELECTOR_XPATH
        ],
        'contact_email' => [
            'selector' => './/*[@class="rma-request-details"]//tr[5]/td[1]',
            'strategy' => Locator::SELECTOR_XPATH
        ]
    ];

    /**
     * Locator for comment list.
     *
     * @var string
     */
    protected $commentHistory = '.rma-comments-history .note-list > li';

    /**
     * Locator for text of single comment.
     *
     * @var string
     */
    protected $commentText = '.note-list-comment';

    /**
     * Get data of tab.
     *
     * @param array|null $fields
     * @param Element|null $element
     * @return array
     */
    public function getDataFormTab($fields = null, Element $element = null)
    {
        $context = $element ? $element : $this->_rootElement;
        $data = $this->dataMapping($fields);

        return array_merge(
            $this->_getData($data, $context),
            $this->getRequestDetails($context),
            ['comment' => $this->getCommentData()]
        );
    }

    /**
     * Return request details.
     *
     * @param Element $context
     * @return array
     */
    protected function getRequestDetails(Element $context)
    {
        $data = [];

        foreach ($this->requestDetails as $fieldName => $locator) {
            $element = $context->find($locator['selector'], $locator['strategy']);
            if ($element->isVisible()) {
                $data[$fieldName] = trim($element->getText());
            }
        }

        if (isset($data['entity_id'])) {
            $data['entity_id'] = str_replace('#', '', $data['entity_id']);
        }
        if (isset($data['order_id'])) {
            $data['order_id'] = str_replace('#', '', $data['order_id']);
        }

        return $data;
    }

    /**
     * Return comments data.
     *
     * @return array
     */
    protected function getCommentData()
    {
        $comments = $this->_rootElement->find($this->commentHistory)->getElements();
        $data = [];

        foreach ($comments as $comment) {
            $data[] = [
                'comment' => trim($comment->find($this->commentText)->getText())
            ];
        }

        return $data;
    }
}
