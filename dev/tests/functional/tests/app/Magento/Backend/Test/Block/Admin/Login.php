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

namespace Magento\Backend\Test\Block\Admin;

use Mtf\Block\Form;
use Mtf\Client\Element\Locator;
use Mtf\Client\Element;
use Mtf\Fixture\InjectableFixture;

/**
 * Class Login
 * Login form for backend user
 *
 * @package Magento\Backend\Test\Block\Admin
 */
class Login extends Form
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
     * 'Log in' button
     *
     * @var string
     */
    protected $submit = '[type=submit]';

    /**
     * Submit login form
     */
    public function submit()
    {
        $this->_rootElement->find($this->submit, Locator::SELECTOR_CSS)->click();
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

    /**
     * Fill in login form
     *
     * @param InjectableFixture $fixture
     */
    public function fillForm(InjectableFixture $fixture)
    {
        foreach($fixture->getData() as $key => $value) {
            $this->_rootElement->find(isset($this->_mapping[$key]) ? $this->_mapping[$key] : '#' . $key)
                ->setValue($value);
        }
    }
}
