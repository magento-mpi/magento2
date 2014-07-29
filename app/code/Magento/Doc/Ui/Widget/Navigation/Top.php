<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Doc\Ui\Widget\Navigation;

use Magento\Framework\View\Element\Template;
use Magento\Framework\View\Block\IdentityInterface;

/**
 * Top navigation
 *
 */
class Top extends Template implements IdentityInterface
{
    /**
     * @return void
     */
    protected function _construct()
    {
        $this->addData(
            [
                //'cache_lifetime' => false,
                'cache_tags' => ['doc-menu']
            ]
        );
    }

    /**
     * Return identifiers for produced content
     *
     * @return array
     */
    public function getIdentities()
    {
        return [\Magento\Doc\Document::CACHE_TAG];
    }

    /**
     * Get Key pieces for caching block content
     *
     * @return array
     */
    public function getCacheKeyInfo()
    {
        $tags = [
            'DOCUMENTATION_NAVIGATION',
            'template' => $this->getTemplate(),
            'name' => $this->getNameInLayout()
        ];
        return $tags;
    }

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
            'Home' => [
                'id' => 'Home',
                'label' => 'Home',
                'path' => '',
                'position' => 10
            ],
            'ReleaseNotes' => [
                'id' => 'ReleaseNotes',
                'label' => 'Release Notes',
                'path' => 'releasenotes',
                'position' => 10
            ],
            'Modules' => [
                'id' => 'Modules',
                'label' => 'Modules Documentation',
                'path' => 'module',
                'position' => 10
            ],
            'ReferenceAPI' => [
                'id' => 'ReferenceAPI',
                'label' => 'Reference API',
                'path' => 'api',
                'position' => 20
            ],
            'Guides' => [
                'id' => 'Guides',
                'label' => 'Guides',
                'position' => 40,
                'children' => [
                    'Developers' => [
                        'id' => 'Developers',
                        'label' => 'Developers Guide',
                        'path' => 'developersguide',
                        'position' => 10
                    ],
                    'Frontend' => [
                        'id' => 'Frontend',
                        'label' => 'Frontend Guide',
                        'path' => 'frontendguide',
                        'position' => 20
                    ],
                    'Administrator' => [
                        'id' => 'Administrator',
                        'label' => 'Administrator Guide',
                        'path' => 'administratorguide',
                        'position' => 30
                    ],
                    'Installation' => [
                        'id' => 'Installation',
                        'label' => 'Installation Guide',
                        'path' => 'installationguide',
                        'position' => 40
                    ],
                    'Upgrade' => [
                        'id' => 'Upgrade',
                        'label' => 'Upgrade Guide',
                        'path' => 'upgradeguide',
                        'position' => 50
                    ],
                    'Migration' => [
                        'id' => 'Migration',
                        'label' => 'Migration Guide',
                        'path' => 'migrationguide',
                        'position' => 60
                    ],
                    'Extension' => [
                        'id' => 'Extension',
                        'label' => 'Extension Guide',
                        'path' => 'extension',
                        'position' => 60
                    ]
                ]
            ],
            'Dictionary' => [
                'id' => 'Dictionary',
                'label' => 'Dictionary',
                'path' => 'dictionary',
                'position' => 50
            ],

            'Howto' => [
                'id' => 'Howto',
                'label' => 'Howto',
                'path' => 'howto',
                'position' => 60
            ],
            'Tools' => [
                'id' => 'Tools',
                'label' => 'Tools',
                'position' => 70,
                'children' => [
                    'FindDenoted' => [
                        'id' => 'FindDenoted',
                        'label' => 'Find denoted items',
                        'position' => 20
                    ],
                    'FindOutdated' => [
                        'id' => 'FindOutdated',
                        'label' => 'Find outdated items',
                        'position' => 10
                    ],
                    'FindMissed' => [
                        'id' => 'FindMissed',
                        'label' => 'Find missed items',
                        'position' => 20
                    ],
                    'CreateSnapshot' => [
                        'id' => 'CreateSnapshot',
                        'label' => 'Create Document Snapshot',
                        'position' => 30
                    ],
                    'GenerateAPI' => [
                        'id' => 'GenerateAPI',
                        'label' => 'Generate Reference API',
                        'position' => 30
                    ],
                    'Export' => [
                        'id' => 'Export',
                        'label' => 'Export',
                        'position' => 40
                    ]
                ]
            ]
        ];

        $html = '';
        foreach ($items as $item) {
            $html .= $this->renderMenuItemHtml($item, $level);
        }

        return $html;
    }
}
