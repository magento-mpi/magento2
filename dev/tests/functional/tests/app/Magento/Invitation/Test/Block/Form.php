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
    protected $sendInvitationsButton = '.action.submit';

    /**
     * Click 'Send Invitations' button
     *
     * @return void
     */
    public function sendInvitations()
    {
        $this->_rootElement->find($this->sendInvitationsButton)->click();
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
                foreach ($value as $index => $email) {
                    $mapping[$key.'_'.$index]['selector'] = isset($mappingFields[$key.'_'.$index]['selector'])
                        ? $mappingFields[$key.'_'.$index]['selector']
                        : sprintf($mappingFields[$key.'_'.$index]['selector'], $index);
                    $mapping[$key.'_'.$index]['strategy'] = isset($mappingFields[$key.'_'.$index]['strategy'])
                        ? $mappingFields[$key.'_'.$index]['strategy']
                        : Element\Locator::SELECTOR_CSS;
                    $mapping[$key.'_'.$index]['input'] = isset($mappingFields[$key.'_'.$index]['input'])
                        ? $mappingFields[$key.'_'.$index]['input']
                        : null;
                    $mapping[$key.'_'.$index]['class'] = isset($mappingFields[$key.'_'.$index]['class'])
                        ? $mappingFields[$key.'_'.$index]['class']
                        : null;
                    $mapping[$key.'_'.$index]['value'] = $fields[$key][$index];
                }
            }
        }
        return array_merge($parentMapping, $mapping);
    }
}
