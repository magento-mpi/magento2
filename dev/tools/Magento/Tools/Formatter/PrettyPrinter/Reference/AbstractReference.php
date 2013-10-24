<?php
/**
 * {license_notice}
 *
 * @copyright {copyright}
 * @license   {license_link}
 */
namespace Magento\Tools\Formatter\PrettyPrinter\Reference;

use Magento\Tools\Formatter\PrettyPrinter\AbstractSyntax;
use Magento\Tools\Formatter\PrettyPrinter\Line;
use Magento\Tools\Formatter\Tree\TreeNode;

/**
 * This class represents the partial line elements, such as references to string or classes.
 * Class AbstractReference
 * @package Magento\Tools\Formatter\PrettyPrinter\Statement
 */
abstract class AbstractReference extends AbstractSyntax
{
    /*
    public function pEncapsList(array $encapsList, $quote) {
        $return = '';
        foreach ($encapsList as $element) {
            if (is_string($element)) {
                $return .= addcslashes($element, "\n\r\t\f\v$" . $quote . "\\");
            } else {
                $return .= '{' . $this->p($element) . '}';
            }
        }

        return $return;
    }
    */
    protected function encapsList($encapsList, $quote, TreeNode $treeNode)
    {
        /** @var Line $line */
        $line = $treeNode->getData()->line;
        foreach ($encapsList as $element) {
            if (is_string($element)) {
                $line->add(addcslashes($element, "\n\r\t\f\v$".$quote."\\"));
            } else {
                $this->resolveNode($element, $treeNode);
            }
        }
    }
}
