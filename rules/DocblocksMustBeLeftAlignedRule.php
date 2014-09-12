<?php

namespace spriebsch\PHPca\Rule;

/**
* Make sure that docblock asterisks are aligned with forward slash.
*/
class DocblocksMustBeLeftAlignedRule extends Rule {

    /**
    * Performs the rule check.
    *
    * @returns null
    */
    protected function doCheck() {
        while ($this->file->seekTokenId(T_DOC_COMMENT)) {
            $docblockToken = $this->file->current();

            $this->file->prev();
            $prevToken = $this->file->current();

            $firstLineIndent = '';
            if ($prevToken->getId() == T_WHITESPACE) {
                preg_match('/([ \t]*)$/', $prevToken->getText(), $matches);
                $firstLineIndent = $matches[1];
            }

            if (preg_match_all('/\n([ \t]*)\*/', $docblockToken->getText(), $matches)) {
                foreach ($matches[1] as $lineIndent) {
                    if ($lineIndent !== $firstLineIndent) {
                        $message = 'Docblock asterisks not aligned with forward slash';
                        $this->addViolation($message, $docblockToken);
                        break;
                    }
                }
            }

            $this->file->seekToken($docblockToken);
            $this->file->next();
        }
    }
}

?>