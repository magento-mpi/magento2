<?php
/**
 * {license_notice}
 *
 * @api
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Mtf\Util\Generate\Factory;

/**
 * Class Block
 *
 * Block Factory generator
 *
 */
class Block extends AbstractFactory
{
    protected $type = 'Block';

    /**
     * Collect Items
     */
    protected function generateContent()
    {
        $blocks = $this->collectItems('Block');
        foreach ($blocks as $block) {
            $this->addBlockToFactory($block);
        }
    }

    /**
     * Add Block to content
     *
     * @param array $item
     */
    protected function addBlockToFactory(array $item)
    {
        list($module, $name) = explode('Test\\Block', $item['class']);
        $methodNameSuffix = $module . $name;
        $methodNameSuffix = $this->_toCamelCase($methodNameSuffix);

        $realClass = $this->_resolveClass($item);
        $fallbackComment = $this->_buildFallbackComment($item, '$element');

        $this->factoryContent .= "\n    /**\n";
        $this->factoryContent .= "     * @return \\{$item['class']}\n";
        $this->factoryContent .= "     */\n";
        $this->factoryContent .= "    public function get{$methodNameSuffix}(\$element, \$driver = null)\n";
        $this->factoryContent .= "    {";

        if (!empty($fallbackComment)) {
            $this->factoryContent .= $fallbackComment . "\n";
        } else {
            $this->factoryContent .= "\n";
        }

        $this->factoryContent .= "        return \$this->objectManager->create('{$realClass}', "
            . "array('element' => \$element, 'driver' => \$driver));";
        $this->factoryContent .= "\n    }\n";

        $this->cnt++;
    }
}
