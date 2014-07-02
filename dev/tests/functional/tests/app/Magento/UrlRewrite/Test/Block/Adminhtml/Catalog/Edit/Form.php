<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\UrlRewrite\Test\Block\Adminhtml\Catalog\Edit;

use Mtf\Client\Element;
use Mtf\Fixture\FixtureInterface;
use Magento\Backend\Test\Block\Widget\Form as FormWidget;

/**
 * Class Form
 * Catalog URL rewrite edit form
 */
class Form extends FormWidget
{
    /**
     * Fill the root form
     *
     * @param FixtureInterface $fixture
     * @param Element|null $element
     * @param string $replace
     * @param FixtureInterface|null $params
     * @return $this
     */
    public function fill(
        FixtureInterface $fixture,
        Element $element = null,
        $replace = null,
        FixtureInterface $params = null
    ) {
        $data = $fixture->getData();
        $getData = $this->getData();
        if (!$getData['target_path'] && !isset($data['target_path'])) {
            $entity = $fixture->getDataFieldConfig('id_path')['source']->getEntity();
            $data['target_path'] = $entity->hasData('identifier')
                ? $entity->getIdentifier()
                : $entity->getUrlKey() . '.html';
        }
        if ($params != null && $params->getDataFieldConfig('id_path')['source']->getEntity() != null) {
            $name = $params->getDataFieldConfig('id_path')['source']->getEntity()->getName();
            $data[$replace] = str_replace('%name%', $name, $data[$replace]);
        }
        // TODO: delete line after removing old fixture
        $fields = isset($data['fields']) ? $data['fields'] : $data;
        $mapping = $this->dataMapping($fields);
        $this->_fill($mapping, $element);

        return $this;
    }
}
