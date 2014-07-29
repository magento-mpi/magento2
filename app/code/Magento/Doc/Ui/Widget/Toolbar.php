<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Doc\Ui\Widget;

use Magento\Framework\View\Element\Template;

class Toolbar extends Template
{
    /**
     * Render menu in HTML
     *
     * @return array
     */
    public function getItems()
    {
        $actions = [
            'freeze' => [
                'action' => 'freeze',
                'label' => 'Freeze'
            ],
            'approve' => [
                'action' => 'approve',
                'label' => 'Approve'
            ],
            'denote' => [
                'action' => 'denote',
                'label' => 'Denote'
            ],
            'add' => [
                'action' => 'add',
                'label' => 'Add media'
            ],
            'save' => [
                'action' => 'save',
                'label' => 'Save'
            ],
            'toggle' => [
                'action' => 'toggle',
                'label' => 'Toggle View'
            ]
        ];

        return $actions;
    }

    /**
     * Render item to html
     *
     * @param array $item
     * @return string
     */
    public function renderItemHtml(array $item)
    {
        // prepare list item html classes
        $classes = [$item['action']];

        // prepare list item attributes
        $attributes = [];
        if (count($classes) > 0) {
            $attributes['class'] = implode(' ', $classes);
        }

        // assemble list item with attributes
        $container = '<li';
        foreach ($attributes as $attrName => $attrValue) {
            $container .= ' ' . $attrName . '="' . str_replace('"', '\"', $attrValue) . '"';
        }
        $container .= '>';

        $html = [];
        $html[] = $container;

        $html[] = '<button action="'.$item['action'].'">' .$item['label'] . '</button>';

        $html[] = '</li>';

        $html = implode("\n", $html);
        return $html;
    }
}
