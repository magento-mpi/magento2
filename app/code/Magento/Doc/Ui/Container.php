<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Doc\Ui;

use Magento\Framework\View\Element\Template;

class Container extends Template
{
    protected function _toHtml()
    {
        $result = parent::_toHtml();
        if (!$result) {
            if ($this->getData('required')) {
                switch ($this->getData('type')) {
                    case 'diagram':
                        $result = "<div class='alert'>Element of type 'diagram' is required! <button class='button'><span>add</span></button></div>";
                        break;
                    case 'article':
                        $result = "<p class='alert'>Element of type 'article' is required!</p>";
                        break;
                    default:
                        $result = "<p class='alert'>Element is required!</p>";
                        break;
                }
            }
        }

        $attributes = [];
        $classes = [];
        if ($this->getChildNames()) {
            $classes[] = 'parent';
        }

        if ($this->checkIfOutdated() || $this->getData('denoted')) {
            $classes[] = 'denoted';
        }

        if ($this->getData('outdated')) {
            $classes[] = 'outdated';
            $attributes['outdated'] = 'outdated';
        }
        if (count($classes) > 0) {
            $attributes['class'] = implode(' ', $classes);
        }

        $attributes['id'] = $this->getNameInLayout();

        $output = '';

        $output .=  '<div module="' . $this->getModuleName() . '" updated_at="' . date('Y-m-d H:i:s') . '">';

        if ($this->getData('label')) {
            $output .= '<h4>' . $this->getData('label') . '</h4>';
        }

        // assemble list item with attributes
        $output .= '<div data-role="doc-item" ';
        foreach ($attributes as $attrName => $attrValue) {
            $output .= ' ' . $attrName . '="' . str_replace('"', '\"', $attrValue) . '"';
        }
        $output .= '>';

        $output .= $result;

        $output .= $this->getChildHtml();

        $output .= '</div>';

        $output .= '</div>';

        return $output;
    }

    /**
     * @return bool
     */
    protected function checkIfOutdated()
    {
        if (!$this->getData('state_hash')) {
            return false;
        }
        $moduleName = $this->getModuleName();
        $targetDir = BP . '/app/code/' . str_replace('_', '/', $moduleName);
        $dirIterator = new \RecursiveIteratorIterator(
            new \RecursiveDirectoryIterator($targetDir, \FilesystemIterator::SKIP_DOTS)
        );
        $hashes = [];
        foreach ($dirIterator as $fileInfo) {
            /** @var $fileInfo \SplFileInfo */
            $fileExt = $fileInfo->getExtension();
            if ($fileExt !== 'xml') {
                $hashes[] = $fileInfo->getMTime();
            }
        }
        return $this->getData('state_hash') !== md5(implode('|', $hashes));
    }

    public function getModuleName()
    {
        if ($this->hasData('module_name')) {
            return $this->getData('module_name');
        }
        list($namespace, $module) = explode('\\', ltrim(get_class($this), '\\'));
        return $namespace . '_' . $module;
    }
}
