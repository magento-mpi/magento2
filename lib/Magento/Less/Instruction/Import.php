<?php
/**
 * {license_notice}
 *
 * @copyright {copyright}
 * @license   {license_link}
 */

namespace Magento\Less\Instruction;

/**
 * Import instruction object
 */
class Import
{
    const TYPE_LESS    = '@import';
    const TYPE_MAGENTO = '//@magento_import';

    /**
     * @var string
     */
    protected $file;

    /**
     * @var bool
     */
    protected $isMagentoImport;

    /**
     * @param string $file
     * @param bool $isMagentoImport
     */
    public function __construct($file, $isMagentoImport)
    {
        $this->file = $file;
        $this->isMagentoImport = $isMagentoImport;
    }

    /**
     * @return bool
     */
    public function isMagentoImport()
    {
        return $this->isMagentoImport;
    }

    /**
     * @return string
     */
    public function getFile()
    {
        return $this->file;
    }

    /**
     * @return string
     */
    public function render()
    {
        $instruction = $this->isMagentoImport ? self::TYPE_MAGENTO : self::TYPE_LESS;

        return sprintf('%s "%s";', $instruction, $this->file);
    }
}
