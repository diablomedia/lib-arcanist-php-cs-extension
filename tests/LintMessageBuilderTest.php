<?php

namespace Paysera\Tests;

use PHPUnit\Framework\TestCase;

class LintMessageBuilderTest extends TestCase
{
    /**
     * @var \LintMessageBuilder
     */
    private $guessLintMessageBuilder;

    /**
     * @var \LintMessageBuilder
     */
    private $exactLintMessageBuilder;

    protected function setUp()
    {
        $this->guessLintMessageBuilder = new \LintMessageBuilder(true);
        $this->exactLintMessageBuilder = new \LintMessageBuilder(false);
    }

    /**
     * @dataProvider dataProviderTestGuessesLintMessages
     *
     * @param string $path
     * @param array $lintResult
     * @param int $messagesCount
     */
    public function testGuessesLintMessages($path, $lintResult, $messagesCount)
    {
        $guessedMessages = $this->guessLintMessageBuilder->buildLintMessages($path, $lintResult['files'][0]);
        $this->assertCount($messagesCount, $guessedMessages);
    }

    /**
     * @dataProvider dataProviderTestBuildsLintMessages
     *
     * @param string $path
     * @param array $lintResult
     * @param int $messagesCount
     */
    public function testBuildsLintMessages($path, $lintResult, $messagesCount)
    {
        $guessedMessages = $this->exactLintMessageBuilder->buildLintMessages($path, $lintResult['files'][0]);
        $this->assertCount($messagesCount, $guessedMessages);
    }

    public function dataProviderTestGuessesLintMessages()
    {
        return [
            [
                __DIR__ . '/diff/simple-diff.php',
                json_decode(file_get_contents(__DIR__ . '/diff/simple-diff.json'), true),
                8,
            ],
            [
                __DIR__ . '/diff/complex-diff-1.php',
                json_decode(file_get_contents(__DIR__ . '/diff/complex-diff-1.json'), true),
                68,
            ],
            [
                __DIR__ . '/diff/complex-diff-2.php',
                json_decode(file_get_contents(__DIR__ . '/diff/complex-diff-2.json'), true),
                17,
            ],
        ];
    }

    public function dataProviderTestBuildsLintMessages()
    {
        return [
            [
                __DIR__ . '/diff/simple-diff.php',
                json_decode(file_get_contents(__DIR__ . '/diff/simple-udiff.json'), true),
                9,
            ],
            [
                __DIR__ . '/diff/complex-diff-1.php',
                json_decode(file_get_contents(__DIR__ . '/diff/complex-udiff-1.json'), true),
                178,
            ],
            [
                __DIR__ . '/diff/complex-diff-2.php',
                json_decode(file_get_contents(__DIR__ . '/diff/complex-udiff-2.json'), true),
                28,
            ],
        ];
    }
}
