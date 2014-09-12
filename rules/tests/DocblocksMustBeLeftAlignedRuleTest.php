<?php

namespace spriebsch\PHPca\Rule;

require_once __DIR__ . '/../DocblocksMustBeLeftAlignedRule.php';

/**
* Tests for the DocblocksMustBeLeftAlignedRule rule.
*/
class DocblocksMustBeLeftAlignedRuleTest extends AbstractRuleTest {

    /**
    * @covers \spriebsch\PHPca\Rule\DocblocksMustBeLeftAlignedRule
    */
    public function testCorrect() {
        $this->init(__DIR__ . '/../testdata/DocblocksMustBeLeftAlignedRule/correct.php');

        $rule = new DocblocksMustBeLeftAlignedRule();
        $rule->check($this->file, $this->result);
        $this->assertEquals(0, $this->result->getNumberOfViolations());
    }

    /**
    * @covers \spriebsch\PHPca\Rule\DocblocksMustBeLeftAlignedRule
    */
    public function testIncorrect() {
        $this->init(__DIR__ . '/../testdata/DocblocksMustBeLeftAlignedRule/incorrect.php');

        $rule = new DocblocksMustBeLeftAlignedRule();
        $rule->check($this->file, $this->result);

        $this->assertEquals(8, $this->result->getNumberOfViolations());
    }
}

?>