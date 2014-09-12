<?php

namespace spriebsch\PHPca\Rule;

require_once __DIR__ . '/../InlineCommentsFormatRule.php';

/**
* Tests for the InlineCommentsFormatRule rule.
*/
class InlineCommentsFormatRuleTest extends AbstractRuleTest {

    /**
    * @covers \spriebsch\PHPca\Rule\InlineCommentsFormatRule
    */
    public function testCorrect() {
        $this->init(__DIR__ . '/../testdata/InlineCommentsFormatRule/correct.php');

        $rule = new InlineCommentsFormatRule();
        $rule->check($this->file, $this->result);

        $this->assertEquals(0, $this->result->getNumberOfViolations());
    }

    /**
    * @covers \spriebsch\PHPca\Rule\InlineCommentsFormatRule
    */
    public function testNoSpaceAtBeginning() {
        $this->init(__DIR__ . '/../testdata/InlineCommentsFormatRule/incorrect.php');

        $rule = new InlineCommentsFormatRule();
        $rule->check($this->file, $this->result);

        $this->assertEquals(12, $this->result->getNumberOfViolations());
    }
}

?>