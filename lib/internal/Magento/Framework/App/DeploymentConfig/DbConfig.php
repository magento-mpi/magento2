<?php
/**
 * {license_notice}
 *
 * @copyright  {copyright}
 * @license    {license_link}
 */

namespace Magento\Framework\App\DeploymentConfig;

class DbConfig extends AbstractSegment
{
    /**#@+
     * Keys for config.php
     */
    const KEY_HOST = 'host';
    const KEY_NAME = 'dbname';
    const KEY_USER = 'username';
    const KEY_PASS = 'password';
    const KEY_PREFIX = 'table_prefix';
    const KEY_MODEL = 'model';
    const KEY_INIT_STATEMENTS = 'initStatements';
    const KEY_ACTIVE = 'active';

    /**
     * Segment key
     */
    const CONFIG_KEY = 'db';

    /**
     * Constructor
     *
     * @param array $data
     * @throws \InvalidArgumentException
     */
    public function __construct(array $data)
    {
        $this->data = [
            self::KEY_PREFIX => '',
            'connection' => [
                'default' => [
                    'name' => 'default',
                    self::KEY_HOST => '',
                    self::KEY_NAME => '',
                    self::KEY_USER => '',
                    self::KEY_PASS => '',
                    self::KEY_MODEL => 'mysql4',
                    self::KEY_INIT_STATEMENTS => 'SET NAMES utf8;',
                    self::KEY_ACTIVE => '1',
                ],
            ]
        ];
        $data = $this->update($data);
        $this->checkData($data);
        parent::__construct($data);
    }

    /**
     * Validate data
     *
     * @param array $data
     * @return void
     * @throws \InvalidArgumentException
     */
    private function checkData(array $data)
    {
        $prefix = $data[self::KEY_PREFIX];
        if ($prefix != '') {
            $prefix = strtolower($prefix);
            if (!preg_match('/^[a-z]+[a-z0-9_]*$/', $prefix)) {
                throw new \InvalidArgumentException(
                    'The table prefix should contain only letters (a-z), numbers (0-9) or underscores (_); '
                    . 'the first character should be a letter.'
                );
            }
        }
        foreach ($data['connection'] as $name => $db) {
            if (empty($db[self::KEY_NAME])) {
                throw new \InvalidArgumentException('The Database Name field cannot be empty.');
            }
            if ($name !== $db['name']) {
                throw new \InvalidArgumentException('Connection name does not match.');
            }
        }
    }

    /**
     * {@inheritdoc}
     */
    public function getKey()
    {
        return self::CONFIG_KEY;
    }
}
