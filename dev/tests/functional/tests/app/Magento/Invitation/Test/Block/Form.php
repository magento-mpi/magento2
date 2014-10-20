<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Invitation\Test\Block;

use Mtf\Client\Element;
use Mtf\Fixture\FixtureInterface;

/**
 * Class Form
 * Send Invitations form
 */
class Form extends \Mtf\Block\Form
{
    /**
     * Send Invitations button
     *
     * @var string
     */
    protected $submit = '.action.submit';

    /**
     * Click 'Send Invitations' button
     *
     * @return void
     */
    public function submit()
    {
        $this->_rootElement->find($this->submit)->click();
    }

    /**
     * Fill form
     *
     * @param FixtureInterface $invitation
     * @param Element|null $element
     * @return $this
     */
    public function fill(FixtureInterface $invitation, Element $element = null)
    {
        $data = $invitation->getData();
        $fields = isset($data['fields']) ? $data['fields'] : $data;
        $mapping = $this->dataMapping($fields);
        $this->_fill($mapping, $element);

        return $this;
    }

    /**
     * Fixture mapping
     *
     * @param array|null $fields
     * @param string|null $parent
     * @return array
     */
    protected function dataMapping(array $fields = null, $parent = null)
    {
        $parentMapping = parent::dataMapping($fields, $parent);
        $mapping = [];
        $mappingFields = ($parent !== null) ? $parent : $this->mapping;
        $data = ($this->mappingMode || null === $fields) ? $mappingFields : $fields;
        foreach ($data as $key => $value) {
            if (is_array($value)) {
                foreach ($value as $k => $email) {
                    $mapping[$key.'_'.$k]['selector'] = isset($mappingFields[$key.'_'.$k]['selector'])
                        ? $mappingFields[$key.'_'.$k]['selector']
                        : sprintf($mappingFields[$key.'_'.$k]['selector'], $k);
                    $mapping[$key.'_'.$k]['strategy'] = isset($mappingFields[$key.'_'.$k]['strategy'])
                        ? $mappingFields[$key.'_'.$k]['strategy']
                        : Element\Locator::SELECTOR_CSS;
                    $mapping[$key.'_'.$k]['input'] = isset($mappingFields[$key.'_'.$k]['input'])
                        ? $mappingFields[$key.'_'.$k]['input']
                        : null;
                    $mapping[$key.'_'.$k]['class'] = isset($mappingFields[$key.'_'.$k]['class'])
                        ? $mappingFields[$key.'_'.$k]['class']
                        : null;
                    $mapping[$key.'_'.$k]['value'] = $fields[$key][$k];
                }
            }
        }
        return array_merge($parentMapping, $mapping);
    }
}
