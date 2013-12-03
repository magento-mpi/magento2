<?php
/**
 * {license_notice}
 *
 * @category    Mtf
 * @package     Mtf
 * @subpackage  functional_tests
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Backend\Test\Block;

use Mtf\Block\Form;
use Mtf\Client\Element\Locator;
use Mtf\Client\Element;

/**
 * Class LoginForm
 * Login form for backend user
 *
 * @package Magento\Backend\Test\Block
 */
class LoginForm extends Form
{
    /**
     * Set locator for password field
     *
     * @var array
     */
    protected $_mapping = array(
        'password' => '#login'
    );

    /**
     * Submit login form
     */
    public function submit()
    {
        $this->_rootElement->find('[type=submit]', Locator::SELECTOR_CSS)->click();
    }

    /**
     * Need to fill only specific fields
     *
     * @param array $fields
     * @param Element $element
     */
    protected function _fill(array $fields, Element $element = null)
    {
        $allowedFields = array('username', 'password');

        $mapping = array();
        foreach ($fields as $fieldName => $data) {
            if (in_array($fieldName, $allowedFields)) {
                $mapping[$fieldName] = $data;
            }
        }
        parent::_fill($mapping, $element);
    }
}
