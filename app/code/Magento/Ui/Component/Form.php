<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Ui\Component;

/**
 * Class Form
 */
class Form extends AbstractView
{
    public function prepare()
    {
        $this->config = $this->configFactory->create(
            [
                'name' => $this->renderContext->getNamespace() . '_' . $this->getNameInLayout(),
                'parentName' => $this->renderContext->getNamespace(),
            ]
        );

        $this->renderContext->getStorage()->addComponentsData($this->config);

        $this->createDataProviders();
        $this->renderContext->getStorage()->addMeta($this->getName(), $this->getData('meta'));
        parent::prepare();
    }

    /**
     * @return array|null
     */
    public function getMeta()
    {
        return $this->renderContext->getStorage()->getMeta($this->getName());
    }

    /**
     * @param array $fieldData
     * @return string
     */
    public function getFieldType(array $fieldData)
    {
        $type = '';
        if (isset($fieldData['data_type'])) {
            $type = $fieldData['data_type'];
        } else if (isset($fieldData['frontend_input'])) {
            $type = $fieldData['frontend_input'];
        }
        return $type;
    }
}
