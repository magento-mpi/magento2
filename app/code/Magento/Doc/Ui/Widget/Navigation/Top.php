<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Doc\Ui\Widget\Navigation;

use Magento\Framework\View\Element\Template;

/**
 * Top navigation
 *
 */
class Top extends Template
{
    /**
     * Render category to html
     *
     * @param array $item
     * @param int $level Nesting level number
     * @return string
     */
    protected function renderMenuItemHtml($item, $level = 0)
    {
        $children = isset($item['children']) ? $item['children'] : [];

        $childrenCount = count($children);
        $hasChildren = $childrenCount > 0;

        // prepare list item html classes
        $classes = [];
        $classes[] = 'level' . $level;
        $classes[] = 'nav-' . $item['position'];
        if ($hasChildren) {
            $classes[] = 'parent';
        }
        $linkClass = '';
        // prepare list item attributes
        $attributes = [];
        if (count($classes) > 0) {
            $attributes['class'] = implode(' ', $classes);
        }

        // assemble list item with attributes
        $htmlLi = '<li';
        foreach ($attributes as $attrName => $attrValue) {
            $htmlLi .= ' ' . $attrName . '="' . str_replace('"', '\"', $attrValue) . '"';
        }
        $htmlLi .= '>';

        $html = array();
        $html[] = $htmlLi;

        if (isset($item['path'])) {
            $url = $this->getUrl('*/' . $item['path']);
        } else {
            $url = '#';
        }
        $html[] = '<a href="' . $url . '"' . $linkClass . '>';
        $html[] = '<span>' . $item['label'] . '</span>';
        $html[] = '</a>';

        // render children
        $htmlChildren = '';
        foreach ($children as $child) {
            $htmlChildren .= $this->renderMenuItemHtml($child, $level + 1);
        }
        if (!empty($htmlChildren)) {
            $html[] = '<ul class="level' . $level . '">';
            $html[] = $htmlChildren;
            $html[] = '</ul>';
        }

        $html[] = '</li>';

        $html = implode("\n", $html);
        return $html;
    }

    /**
     * Render menu in HTML
     *
     * @param int $level Level number for list item class to start from
     * @return string
     */
    public function renderMenuHtml($level = 0)
    {
        $items = [
            'home' => [
                'id' => 'home',
                'label' => 'Home',
                'path' => '',
                'position' => 10
            ],

            'api' => [
                'id' => 'api',
                'label' => 'API',
                'position' => 20,
                'children' => [
                    'reference' => [
                        'id' => 'reference',
                        'label' => 'Reference API',
                        'path' => 'api/reference',
                        'position' => 10
                    ],
                    'module' => [
                        'id' => 'module',
                        'label' => 'Modules API',
                        'path' => 'api/module',
                        'position' => 20
                    ]
                ]
            ],

            'guide' => [
                'id' => 'Guides',
                'label' => 'Guides',
                'position' => 30,
                'children' => [
                    'developer' => [
                        'id' => 'developer',
                        'label' => 'Developers Guide',
                        'path' => 'guide/developer',
                        'position' => 10
                    ],
                    'frontend' => [
                        'id' => 'frontend',
                        'label' => 'Frontend Guide',
                        'path' => 'guide/frontend',
                        'position' => 20
                    ],
                    'administrator' => [
                        'id' => 'administrator',
                        'label' => 'Administrator Guide',
                        'path' => 'guide/administrator',
                        'position' => 30
                    ],
                    'installation' => [
                        'id' => 'installation',
                        'label' => 'Installation Guide',
                        'path' => 'guide/installation',
                        'position' => 40
                    ],
                    'upgrade' => [
                        'id' => 'upgrade',
                        'label' => 'Upgrade Guide',
                        'path' => 'guide/upgrade',
                        'position' => 50
                    ],
                    'migration' => [
                        'id' => 'migration',
                        'label' => 'Migration Guide',
                        'path' => 'guide/migration',
                        'position' => 60
                    ],
                    'extension' => [
                        'id' => 'extension',
                        'label' => 'Extension Guide',
                        'path' => 'guide/extension',
                        'position' => 60
                    ]
                ]
            ],

            'tools' => [
                'id' => 'tools',
                'label' => 'Tools',
                'position' => 40,
                'children' => [
                    'Export' => [
                        'id' => 'Export',
                        'label' => 'Export',
                        'position' => 40
                    ]
                ]
            ],

            'help' => [
                'id' => 'help',
                'label' => 'Help',
                'position' => 50,
                'children' => [
                    'howto' => [
                        'id' => 'howto',
                        'label' => 'How-to',
                        'path' => 'help/howto',
                        'position' => 10
                    ],
                    'dictionary' => [
                        'id' => 'dictionary',
                        'label' => 'Dictionary',
                        'path' => 'help/dictionary',
                        'position' => 20
                    ],

                    'example' => [
                        'id' => 'example',
                        'label' => 'Examples',
                        'path' => 'help/example',
                        'position' => 30
                    ],
                ]
            ],
        ];

        $html = '';
        foreach ($items as $item) {
            $html .= $this->renderMenuItemHtml($item, $level);
        }

        return $html;
    }
}
