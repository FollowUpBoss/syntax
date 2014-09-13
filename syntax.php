<?php
/**
* FollowUpBoss: Real Estate Lead Management Software.
*
* @author        Anthony Gentile <anthony@followupboss.com>
* @copyright     Copyright (c) 2014, Enchant LLC.
* @license       Property of Enchant LLC. All rights reserved.
*/
namespace FUBSyntax;

require dirname(__FILE__) . '/lib/phpca/src/Autoload.php';

use Exception;
use spriebsch\PHPca\Result;
use spriebsch\PHPca\Application;
use spriebsch\PHPca\Configuration;
use spriebsch\PHPca\ProgressPrinterInterface;

/**
* Runs syntax checks against files. This will validate
* against the Lithium and FUB coding, documentation and testing standards.
*/
class Syntax implements ProgressPrinterInterface {

    /**
    * Aura\Cli\Stdio object
    *
    * @var object
    */
    protected $stdio;

    /**
    * Run syntax check against a file or directory.
    *
    * @param string $path Absolute path to file or directory.
    * @param object $stdio Aura\Cli\Stdio object.
    * @return boolean
    */
    public function run($path, $stdio) {
        $this->stdio = $stdio;

        $app = new Application(getcwd());
        $app->registerProgressPrinter($this);

        $file = __DIR__ . '/fub-standard.ini';

        $config = new Configuration(getcwd());
        $parsed = parse_ini_file($file, true);
        if ($parsed['PHPca']['additional_rules'] == 'rules') {
            $parsed['PHPca']['additional_rules'] = __DIR__ . '/rules';
        }
        $config->setStandard($parsed);
        $config->setConfiguration(array());

        $php = PHP_BINDIR . '/' . 'php';

        try {
            $result = $app->run($php, $path, $config);
        } catch (Exception $e) {
            $this->stdio->outln($message = $e->getMessage());
            return $message == 'No PHP files to analyze';
        }

        return !$result->hasErrors();
    }

    public function showProgress($file, Result $result, Application $application) {
        $self = $this;

        $format = function ($result) use ($self) {
            return sprintf(
                '%1$4s| %2$3s| %4$s',
                $result->getLine() ?: '??',
                $result->getColumn() ?: '??',
               '',
                $result->getMessage() ?: '??'
            );
        };

        if ($result->wasSkipped($file)) {
            $this->stdio->outln("[<<blue>>skip<<reset>>] {$file}");
        } elseif ($result->hasLintError($file)) {
            $this->stdio->outln("[<<blue>>exception<<reset>>] {$file}");
            $this->stdio->outln($format($result->getLintError($file)));
        } elseif ($result->hasRuleError($file)) {
            $this->stdio->outln("[<<blue>>exception<<reset>>] {$file}");
            foreach ($result->getRuleErrors($file) as $error) {
                $this->stdio->outln($format($error));
            }
        } elseif ($result->hasViolations($file)) {
            $this->stdio->outln("[<<red>>fail<<reset>>] {$file}");
            foreach ($result->getViolations($file) as $violation) {
                $this->stdio->outln($format($violation));
            }
        } else {
            $this->stdio->outln("[<<green>>pass<<reset>>] {$file}");
        }
    }
}

?>