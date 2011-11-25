<?php
/**
 * {license_notice}
 *
 * @category   build
 * @package    publisher
 * @copyright  {copyright}
 * @license    {license_link}
 */

require dirname(__FILE__) . '/ScriptAbstract.php';

/**
 * Commit changes and push changes into origin repository
 *
 */
class Publisher extends ScriptAbstract {

    /**
     * Commit changes and push changes into origin repository
     *
     * @return void
     */
    public function run()
    {
        $this->_callGitCommand(
            'add .',
            'Can not add changes to the list.'
        );

        $thereIsChangesForCommit = false;
        $output = $this->_callGitCommand(
            'status',
            'Can not get information about items for commit.'
        );
        foreach ($output as $line) {
            if (strpos($line, 'Changes to be committed')) {
                $thereIsChangesForCommit = true;
                break;
            }
        }

        if ($thereIsChangesForCommit) {
            $this->_callGitCommand(
                'commit --allow-empty-message  --message=""',
                'Can not commit.'
            );
            $this->_callGitCommand(
                'push origin master',
                'Can not push.'
            );
        }
    }
}
