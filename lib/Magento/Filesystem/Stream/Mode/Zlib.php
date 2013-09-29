<?php
/**
 * Magento filesystem zlib stream mode
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Filesystem\Stream\Mode;

class Zlib extends \Magento\Filesystem\Stream\Mode
{
    /**
     * Compression ratio
     *
     * @var int
     */
    protected $_ratio = 1;

    /**
     * Compression strategy
     *
     * @var string
     */
    protected $_strategy = '';

    /**
     * @param string $mode
     */
    public function __construct($mode)
    {
        $searchPattern = '/(r|w|a|x|c)(b)?(\+)?(\d*)(f|h)?/';
        preg_match($searchPattern, $mode, $ratios);
        if (count($ratios) > 4 && $ratios[4]) {
            $this->_ratio = (int)$ratios[4];
        }
        if (count($ratios) == 6) {
            $this->_strategy = $ratios[5];
        }
        $mode = preg_replace($searchPattern, '\1\2\3', $mode);
        parent::__construct($mode);
    }

    /**
     * Get compression ratio
     *
     * @return int
     */
    public function getRatio()
    {
        return $this->_ratio;
    }

    /**
     * Get compression strategy
     *
     * @return null|string
     */
    public function getStrategy()
    {
        return $this->_strategy;
    }
}
