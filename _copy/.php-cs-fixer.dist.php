<?php
/**
 * @package  trade .php-cd-fixer.dist.php
 * @copyright 11.05.2024 Zhalyaletdinov Vyacheslav evil_tut@mail.ru
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

declare(strict_types=1);

use PhpCsFixer\RuleSet\Sets\PSR12Set;

$finder = (new PhpCsFixer\Finder())->in(__DIR__)->exclude(
    ['var', 'vendor', 'node_modules', '.git', '.idea', '.vscode', '.phpunit.', 'storage']
);

$default = (new PSR12Set())->getRules();
$custom_rules = [
//    'no_spaces_after_function_name' => true
];

return (new PhpCsFixer\Config())->setRules(array_merge($default, $custom_rules))->setFinder($finder);