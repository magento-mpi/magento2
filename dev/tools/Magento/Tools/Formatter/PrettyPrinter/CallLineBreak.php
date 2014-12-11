<?php
/**
 * @copyright Copyright (c) 2014 X.commerce, Inc. (http://www.magentocommerce.com)
 */
namespace Magento\Tools\Formatter\PrettyPrinter;

/**
 * Class CallLineBreak
 *
 * array()
 *
 * array(x1,x2,x3x)
 * array(x
 * 1,x
 * 2,x
 * 3x
 * )
 *
 * 1 blank	HardIndent
 * 2 space	HardIndent
 * 3 space	HardIndent
 * 4 blank  Hard
 */
class CallLineBreak extends ConditionalLineBreak
{
    const LINEBREAK_ID = 'conditionallb';

    /**
     * {@inheritdoc}
     */
    public function __construct()
    {
        parent::__construct([]);
    }

    /**
     * This method returns the value for the break based on the passed in information.
     *
     * @param int $level Indicator for the level for which the break is being resolved.
     * @param int $index Zero based index of this break occurrence in the line.
     * @param int $total Total number of this break occurrences in the line.
     * @param array &$lineBreakData Data that the line break can use.
     * @return HardIndentLineBreak|HardLineBreak|string|false
     */
    public function getValue($level, $index, $total, array &$lineBreakData)
    {
        // if the level is set to be more than the default make sure only this instance can write the advanced version
        if ($level > 0) {
            // only process the first instance of the call line break
            if (!isset(
                $lineBreakData[self::LINEBREAK_ID]
            ) || $this->getGroupingId() === $lineBreakData[self::LINEBREAK_ID]
            ) {
                // save off which instance is being processed
                $lineBreakData[self::LINEBREAK_ID] = $this->getGroupingId();
                // determine the resolution of the break
                $result = $this->getValueByLevel($level, $index, $total);
                // clear the linebreak if this is the last one
                if ($index >= $total - 1) {
                    unset($lineBreakData[self::LINEBREAK_ID]);
                }
            } else {
                // return a flag indicating that this is not being resolved
                $result = false;
            }
        } else {
            // determine the resolution of the break
            $result = $this->getValueByLevel($level, $index, $total);
        }
        return $result;
    }

    /**
     * This method returns a flag indicating that when placed in a list, an additional instance is
     * required after the list.
     *
     * @return bool
     */
    public function isAfterListRequired()
    {
        return true;
    }

    /**
     * This method returns an id used to group line breaks occurring in the same line together.
     * This is typically either the class name or the instance id.
     *
     * @return string
     */
    public function getGroupingId()
    {
        return spl_object_hash($this);
    }

    /**
     * This method returns a sort order indication as to the order in which breaks should be processed.
     *
     * @return int Order relative to other classes overriding this method.
     */
    public function getSortOrder()
    {
        return 200;
    }

    /**
     * This method returns the value based on the level passed in.
     *
     * @param int $level Indicator for the level for which the break is being resolved.
     * @param int $index Zero based index of this break occurrence in the line.
     * @param int $total Total number of this break occurrences in the line.
     * @return HardIndentLineBreak|HardLineBreak|string
     */
    protected function getValueByLevel($level, $index, $total)
    {
        switch ($level) {
            case 0:
                switch ($index) {
                    case 0:
                    case $total - 1:
                        $result = '';
                        break;
                    default:
                        $result = ' ';
                        break;
                }
                break;
            default:
                // this class only handle 2 levels, so treat later levels the same as the second
                switch ($index) {
                    case $total - 1:
                        $result = new HardLineBreak();
                        break;
                    default:
                        $result = new HardIndentLineBreak();
                        break;
                }
                break;
        }
        return $result;
    }
}
