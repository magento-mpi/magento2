<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Sales\Model\Email;

/**
 * Sales email template model
 */
class Template extends \Magento\Email\Model\Template
{
    /**
     * @param string $template
     * @param array $variables
     * @return string
     */
    public function getInclude($template, array $variables)
    {
        $filename = $this->_viewFileSystem->getTemplateFileName($template);
        if (!$filename) {
            return '';
        }
        extract($variables);
        ob_start();
        include $filename;
        return ob_get_clean();
    }
}
