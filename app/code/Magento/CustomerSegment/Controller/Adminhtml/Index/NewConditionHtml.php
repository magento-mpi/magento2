<?php
/**
 *
 * @copyright Copyright (c) 2014 X.commerce, Inc. (http://www.magentocommerce.com)
 */
namespace Magento\CustomerSegment\Controller\Adminhtml\Index;

class NewConditionHtml extends \Magento\CustomerSegment\Controller\Adminhtml\Index
{
    /**
     * Add new condition
     *
     * @return void
     */
    public function execute()
    {
        $typeArr = explode('|', str_replace('-', '/', $this->getRequest()->getParam('type')));
        $type = $typeArr[0];
        $id = $this->getRequest()->getParam('id');

        $segment = $this->_objectManager->create('Magento\CustomerSegment\Model\Segment');
        $segment->setApplyTo((int)$this->getRequest()->getParam('apply_to'));

        $model = $this->_conditionFactory->create(
            $type
        )->setId(
            $id
        )->setType(
            $type
        )->setRule(
            $segment
        )->setPrefix(
            'conditions'
        );
        if (!empty($typeArr[1])) {
            $model->setAttribute($typeArr[1]);
        }

        $html = '';
        if ($model instanceof \Magento\Rule\Model\Condition\AbstractCondition) {
            $model->setJsFormObject($this->getRequest()->getParam('form'));
            $html = $model->asHtmlRecursive();
        }
        $this->getResponse()->setBody($html);
    }
}
