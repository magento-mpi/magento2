<?php
    /**
     * {license_notice}
     *
     * @category    Magento
     * @package     Mage_NewsletterAdmin
     * @subpackage  functional_tests
     * @copyright   {copyright}
     * @license     {license_link}
     */

    /**
     * Helper class
     *
     * @package     selenium
     * @subpackage  tests
     * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
     */
class Community2_Mage_Newsletter_Helper extends Mage_Newsletter_Helper
{
    /**
     * Create Newsletter Templates
     *
     * Preconditions: 'New Newsletter Template' page is opened.
     *
     * @param array|string $data
     *
     */
    public function createNewsletterTemplate($data)
    {
        if (is_string($data)) {
            $elements = explode('/', $data);
            $fileName = (count($elements) > 1)
                ? array_shift($elements)
                : '';
            $data = $this->loadDataSet($fileName, implode('/', $elements));
        }

        $this->clickButton('add_new_template');
        $this->fillFieldSet($data, 'newsletter_add_template');
        $this->saveForm('save_template');
    }
}
