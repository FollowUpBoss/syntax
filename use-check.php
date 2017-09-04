<?php

class Check {

    public $exitCode = 0;

    public $verbose = false;

    public function __construct($filename) {
        $this->filename = $filename;
    }

    public function log($priority, $msg, $node) {
        $prefix = preg_replace(",^/var/www/fub/,", "", $this->filename)
            . ":" . $node->lineno . " " . strtoupper($priority) . ": ";

        $this->exitCode = 1;

        echo "$prefix $msg\n";

        if ($this->verbose) {
            print_r($node);
        }
    }

    public static function walker($root) {
        if (!is_object($root) || !property_exists ($root, 'children')) {
            return;
        }

        yield $root;

        foreach ($root->children as $child) {
            $nodes = static::walker($child);
            foreach ($nodes as $n) {
                yield $n;
            }
        }
    }

    public function run () {
        $root = ast\parse_file($this->filename, $version=50);

        $iterator = static::walker($root);

        $imports = [];
        $classUsed = [];

        foreach ($iterator as $i) {
            if ($i->kind == ast\AST_USE_ELEM) {
                if (empty($i->children['alias'])) {
                    $importName = preg_replace(",^.*\\\\,", "", $i->children['name']);
                } else {
                    $importName = $i->children['alias'];
                }

                $imports[$importName] = $i;
            }

            if (!empty($i->children["class"])
                && !empty($i->children["class"]->children["name"])
                && $i->children["class"]->kind == ast\AST_NAME
            ) {
                $key = $i->children["class"]->children["name"];

                /* echo  "className: $key, kind: " */
                /*     . ast\get_kind_name($i->kind) . ", " */
                /*     . ast\get_kind_name($i->children["class"]->kind) . "\n"; */

                if (empty($classUsed[$key])) {
                    $classUsed[$key] = $i;
                }
            }
        }

        foreach ($classUsed as $className => $node) {
            if (in_array($className, ['parent', 'self', 'static'])) {
                continue;
            }

            if (strpos($className, "\\") !== false) {
                // assume an absolute import like "\Httpful\Bootstrap" which doesn't need a 'use'
                // FIXME: ast strips the leading "\" from the name, figure out how to properly
                // distinguish an absolute import form a relative one.
                continue;
            }

            // FIXME: look for class definitions in the current file, instead of
            // relying on filename == classname
            if (basename($this->filename, '.php') == $className) {
                continue;
            }

            if (!array_key_exists($className, $imports)) {
                $this->log("error", "{$className} not imported", $node);
            }
        }

        foreach ($imports as $className => $node) {
            if (!array_key_exists($className, $classUsed)) {
                $this->log("warning", "{$className} imported, but not used", $node);
            }
        }

        return $this->exitCode;
    }
}

function main() {
    global $argv;

    $filenames = [];
    $verbose = false;

    array_shift($argv);
    foreach ($argv as $a) {
        if ($a == '--verbose') {
            $verbose = true;
        } else {
            $filenames[] = realpath($a);
        }
    }

    $errors = 0;
    foreach ($filenames as $fn) {
        $check = new Check($fn);
        $check->verbose = $verbose;
        $errors += $check->run();
    }

    exit ($errors > 0 ? 1 : 0);
}

main();
