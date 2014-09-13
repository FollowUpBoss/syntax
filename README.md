Follow Up Boss Syntax Checker
=============================

### Installation ###

```
composer update
```

### Usage ###

```
syntax path/to/file/or/folder
```
This command will run the syntax checker against a specific folder or file.

You may find it easier to alias this command to your liking using your ```~/.bashrc``` file. e.g.

```
alias syntax='/path/to/syntax'
```

* * *

Git precommit example file is included, you can adjust this as need for your project and symlink it to your .git/hooks directory.
