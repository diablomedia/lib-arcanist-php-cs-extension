<?php

class PhpCsFixerLinter extends \ArcanistExternalLinter
{
    /**
     * @var array
     */
    private $defaultFlags = [
        '--verbose',
        '--dry-run',
        '--diff',
        '--format=json',
    ];

    /**
     * @var string Config file path
     */
    private $configFile = null;

    /**
     * @var LintMessageBuilder
     */
    private $lintMessageBuilder;

    public function getLinterName()
    {
        return 'php-cs-fixer';
    }

    public function getInfoName()
    {
        return 'php-cs-fixer';
    }

    public function getInfoURI()
    {
        return 'https://github.com/FriendsOfPHP/PHP-CS-Fixer';
    }

    public function getInfoDescription()
    {
        return pht(
            'The PHP Coding Standards Fixer tool fixes most issues in your code' .
            'when you want to follow the PHP coding standards as defined in' .
            'the PSR-1 and PSR-2 documents and many more.'
        );
    }

    public function getLinterConfigurationName()
    {
        return 'php-cs-fixer';
    }

    public function getMandatoryFlags()
    {
        $guessMessages = true;

        if (version_compare($this->getVersion(), '2.8.0', '>=')) {
            $this->defaultFlags[] = '--diff-format=udiff';
            $guessMessages = false;
        }

        $this->lintMessageBuilder = new LintMessageBuilder($guessMessages);

        $flags = array(
            'fix',
        );

        if ($this->configFile !== null) {
            $flags = array_merge(
                $flags,
                array_unique($this->defaultFlags),
                [sprintf('--config=%s', $this->configFile)]
            );
        }

        return $flags;
    }

    public function getInstallInstructions()
    {
        return 'By installing this package, you\'ve already installed all dependencies!';
    }

    public function setLinterConfigurationValue($key, $value)
    {
        switch ($key) {
            case 'config':
                $this->configFile = $value;
                return;
            default:
                parent::setLinterConfigurationValue($key, $value);
                return;
        }
    }

    public function getDefaultBinary()
    {
        return 'php-cs-fixer';
    }

    /**
     * @return string|null
     */
    public function getVersion()
    {
        list($stdout) = execx('%C --version', $this->getExecutableCommand());

        $version = null;
        if (preg_match('#PHP CS Fixer (\d+\.\d+\.\d+\.*)#i', $stdout, $matches)) {
            $version = $matches[1];
        }

        return $version;
    }

    public function parseLinterOutput($path, $err, $stdout, $stderr)
    {
        $json = phutil_json_decode($stdout);
        $messages = [];
        foreach ($json['files'] as $fix) {
            $messages = array_merge($messages, $this->lintMessageBuilder->buildLintMessages($path, $fix));
        }

        return $messages;
    }

    public function getLinterConfigurationOptions()
    {
        $options = array(
            'config' => array(
                'type' => 'optional string',
                'help' => pht(
                    'The path to your .php_cs file. Will be provided as -config <path> to php-cs-fixer.'
                ),
            ),
        );
        return $options + parent::getLinterConfigurationOptions();
    }

    public function shouldExpectCommandErrors()
    {
        return true;
    }

    protected function getPathArgumentForLinterFuture($path)
    {
        $root = $this->getEngine()->getWorkingCopy()->getProjectRoot();

        return str_replace($root . '/', '', $path);
    }
}
