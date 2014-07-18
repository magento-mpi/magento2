<?php
/**
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Catalog\Controller\Adminhtml\Product\Action\Attribute;

class Validate extends \Magento\Catalog\Controller\Adminhtml\Product\Action\Attribute
{
    /**
     * Attributes validation action
     *
     * @return void
     */
    public function execute()
    {
        $response = new \Magento\Framework\Object();
        $response->setError(false);
        $attributesData = $this->getRequest()->getParam('attributes', array());
        $data = new \Magento\Framework\Object();

        try {
            if ($attributesData) {
                foreach ($attributesData as $attributeCode => $value) {
                    $attribute = $this->_objectManager->get('Magento\Eav\Model\Config')
                        ->getAttribute('catalog_product', $attributeCode);
                    if (!$attribute->getAttributeId()) {
                        unset($attributesData[$attributeCode]);
                        continue;
                    }
                    $data->setData($attributeCode, $value);
                    $attribute->getBackend()->validate($data);
                }
            }
        } catch (\Magento\Eav\Model\Entity\Attribute\Exception $e) {
            $response->setError(true);
            $response->setAttribute($e->getAttributeCode());
            $response->setMessage($e->getMessage());
        } catch (\Magento\Framework\Model\Exception $e) {
            $response->setError(true);
            $response->setMessage($e->getMessage());
        } catch (\Exception $e) {
            $this->messageManager->addException(
                $e,
                __('Something went wrong while updating the product(s) attributes.')
            );
            $this->_view->getLayout()->initMessages();
            $response->setError(true);
            $response->setHtmlMessage($this->_view->getLayout()->getMessagesBlock()->getGroupedHtml());
        }

        $this->getResponse()->representJson($response->toJson());
    }
}
