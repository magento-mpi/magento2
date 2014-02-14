<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Less;

/**
 * Interface for pre-processing less instruction
 */
interface PreProcessorInterface
{
    /**
     * Process less content to adapt magento view system
     *
     * @param PreProcessor\File $lessFile
     * @param string $lessContent
     * @return string of processed content
     */
    public function process(PreProcessor\File $lessFile, $lessContent);
}
