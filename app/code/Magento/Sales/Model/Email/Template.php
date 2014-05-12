<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Sales\Model\Email;

class Template extends \Magento\Email\Model\Template
{
    /**
     * @param string $template
     * @param array $variables
     * @return string
     */
    public function getInclude($template, array $variables)
    {
        $filename = $this->_viewFileSystem->getFilename($template);
        if (!$filename) {
            return '';
        }
        extract($variables);
        ob_start();
        include $filename;
        return ob_get_clean();
    }
}
