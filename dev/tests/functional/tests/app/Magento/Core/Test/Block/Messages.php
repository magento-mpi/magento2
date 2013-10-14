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

namespace Magento\Core\Test\Block;

use Mtf\Block\Block;
use Mtf\Fixture\DataFixture;

/**
 * Class Messages
 * Messages block
 *
 * @package Magento\Core\Test\Block
 */
class Messages extends Block
{
    /**
     * Success message
     *
     * @var string
     */
    private $successMessage;

    /**
     * Initialize block elements
     */
    protected function _init()
    {
        //Elements
        $this->successMessage = '[data-ui-id=messages-message-success]';
    }

    /**
     * Check for success message
     *
     * @param DataFixture $fixture
     *
     * @return bool
     */
    public function waitForSuccessMessage(DataFixture $fixture)
    {
        $this->waitForElementVisible($this->successMessage);
    }
}
