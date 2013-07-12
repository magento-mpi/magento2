<?php
/**
 * {license_notice}
 *
 * @copyright  {copyright}
 * @license    {license_link}
 */

class Layout_Merger
{
    /**
     * @var string
     */
    private $_template;

    /**
     * @param string $template
     */
    public function __construct($template = '<layouts>%s</layouts>')
    {
        $this->_template = $template;
    }

    /**
     * Combine multiple layout handles into a single XML string
     *
     * @param array|Layout_Handle[] $handles
     * @return string
     */
    public function merge(array $handles)
    {
        // group handles by name
        $groups = array();
        foreach ($handles as $handle) {
            $groups[$handle->getName()][] = $handle;
        }

        $result = '';
        foreach ($groups as $handleName => $handlesInGroup) {
            $handleDeclaration = $this->_findHandleDeclaration($handlesInGroup);
            $result .= '<' . $handleName . $handleDeclaration->renderAttributes() . '>';
            /** @var Layout_Handle $handle */
            foreach ($handlesInGroup as $handle) {
                $result .= $handle->renderInnerXml();
            }
            $result .= '</' . $handleName . '>';
        }

        $result = sprintf($this->_template, $result);
        return $result;
    }

    /**
     * Find a layout handle with attributes, checking for attributes integrity on the way
     *
     * @param array|Layout_Handle[] $handles
     * @return Layout_Handle|null
     * @throws Exception
     */
    protected function _findHandleDeclaration(array $handles)
    {
        /** @var Layout_Handle $result */
        $result = null;
        foreach ($handles as $handle) {
            $handleAttributes = $handle->renderAttributes();
            if ($handleAttributes) {
                if ($result) {
                    if ($handleAttributes !== $result->renderAttributes()) {
                        throw new Exception("Attributes for layout handle '{$handle->getName()}' are ambiguous.");
                    }
                }
                $result = $handle;
            }
        }
        if (!$result && $handles) {
            $result = reset($handles);
        }
        return $result;
    }
}
