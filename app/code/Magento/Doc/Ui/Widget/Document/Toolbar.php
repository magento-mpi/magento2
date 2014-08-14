<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Doc\Ui\Widget\Document;

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
            'bold' => [
                'action' => 'bold',
                'label' => 'Bold'
            ],
            'italic' => [
                'action' => 'italic',
                'label' => 'Italic'
            ],
            'insertUnorderedList' => [
                'action' => 'insertUnorderedList',
                'label' => 'Insert Unordered List'
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
        // prepare list item attributes
        $attributes = [];
        $attributes['class'] = 'toolbar-action';
        $attributes['action'] = $item['action'];
        $attributes['title'] = $item['label'];

        // assemble list item with attributes
        $container = '<li';
        foreach ($attributes as $attrName => $attrValue) {
            $container .= ' ' . $attrName . '="' . str_replace('"', '\"', $attrValue) . '"';
        }
        $container .= '>';

        $html = [];
        $html[] = $container;

        $html[] = '</li>';

        $html = implode("\n", $html);
        return $html;
    }
}
