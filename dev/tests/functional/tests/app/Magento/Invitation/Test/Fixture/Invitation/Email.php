<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Invitation\Test\Fixture\Invitation;

use Mtf\Fixture\FixtureInterface;

/**
 * Class Email
 * Prepare data for email field in Invitation fixture
 */
class Email implements FixtureInterface
{
    /**
     * Resource data
     *
     * @var string
     */
    protected $data;

    /**
     * Data set configuration settings
     *
     * @var array
     */
    protected $params;

    /**
     * @param array $params
     * @param string $data
     */
    public function __construct(array $params, $data)
    {
        $this->params = $params;
        $emails = explode(',', $data);
        $data = [];
        foreach ($emails as $key => $value) {
            $data['email_' . ($key + 1)] = trim($value);
        }
        $this->data = $data;
    }

    /**
     * Persists prepared data into application
     *
     * @return void
     */
    public function persist()
    {
        //
    }

    /**
     * Return prepared data
     *
     * @param string|null $key [optional]
     * @return string
     *
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function getData($key = null)
    {
        return $this->data;
    }

    /**
     * Return data set configuration settings
     *
     * @return array
     */
    public function getDataConfig()
    {
        return $this->params;
    }
}
