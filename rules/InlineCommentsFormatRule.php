<?php

namespace spriebsch\PHPca\Rule;

/**
* Make sure that single line inline comment rules are enforced.
*/
class InlineCommentsFormatRule extends Rule {

    /**
    * Performs the rule check.
    *
    * @returns null
    */
    protected function doCheck() {
        while ($this->file->seekTokenId(T_COMMENT)) {
            $commentToken = $this->file->current();
            $text = rtrim($commentToken->getText());

            if (strlen($text) > 2 && substr($text, 0, 2) == '//') {
                if (!preg_match('/^\/\/ /', $text)) {
                    $this->addViolation("Inline comments must begin with a space", $commentToken);
                } else {
                    $this->file->prev();
                    $this->file->seekTokenId(T_COMMENT, true);
                    $prevCommentToken = $this->file->current();

                    $this->file->seekToken($commentToken);
                    $this->file->next();
                    $this->file->seekTokenId(T_COMMENT);
                    $nextCommentToken = $this->file->current();

                    $commentAbove = $prevCommentToken->getLine() == ($commentToken->getLine() - 1);
                    $commentBelow = $nextCommentToken->getLine() == ($commentToken->getLine() + 1);

                    // Single line inline comments must not start with capitalization and must not end in
                    // pucuation.
                    if (!$commentAbove && !$commentBelow) {
                        if (!preg_match('/^\/\/ ([^A-Z]|[A-Z]{2,})/', $text)) {
                            $this->addViolation("Inline comments must not start with capitalization", $commentToken);
                        } else if (preg_match('/\W$/', $text)) {
                            $this->addViolation("Inline comments must not end in punctuation", $commentToken);
                        }
                    }
                }
            }

            $this->file->seekToken($commentToken);
            $this->file->next();
        }
    }
}

?>