<?php
/**
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\CatalogWidget\Controller\Adminhtml\Product\Widget;

use Magento\Rule\Model\Condition\AbstractCondition;

/**
 * Class Conditions
  */
class Conditions extends \Magento\CatalogWidget\Controller\Adminhtml\Product\Widget
{
    /**
     * @return void
     */
    public function execute()
    {
        $id = $this->getRequest()->getParam('id');
        $typeArr = explode('|', str_replace('-', '/', $this->getRequest()->getParam('type')));
        $type = $typeArr[0];

        $model = $this->_objectManager->create($type)
            ->setId($id)
            ->setType($type)
            ->setRule(
                $this->_objectManager->create('Magento\CatalogWidget\Model\Rule')
            )->setPrefix('conditions');

        if (!empty($typeArr[1])) {
            $model->setAttribute($typeArr[1]);
        }

        if ($model instanceof AbstractCondition) {
            $model->setJsFormObject($this->getRequest()->getParam('form'));
            $html = $model->asHtmlRecursive();
        } else {
            $html = '';
        }
        $this->getResponse()->setBody($html);
    }
}
