<?php

// @see https://dev.to/zaratedev/configure-php-cs-fixer-for-laravel-on-visual-code-5a3j

return PhpCsFixer\Config::create()
    ->setRules([
        'array_syntax' => ['syntax' => 'short']
    ]);