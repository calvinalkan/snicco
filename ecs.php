<?php

declare(strict_types=1);

use PhpCsFixer\Fixer\ClassNotation\SelfAccessorFixer;
use PhpCsFixer\Fixer\FunctionNotation\NativeFunctionInvocationFixer;
use PhpCsFixer\Fixer\Import\OrderedImportsFixer;
use PhpCsFixer\Fixer\Phpdoc\PhpdocToCommentFixer;
use PhpCsFixer\Fixer\Phpdoc\PhpdocTypesOrderFixer;
use PhpCsFixer\Fixer\PhpUnit\PhpUnitMethodCasingFixer;
use PhpCsFixer\Fixer\PhpUnit\PhpUnitStrictFixer;
use PhpCsFixer\Fixer\PhpUnit\PhpUnitTestAnnotationFixer;
use PhpCsFixer\Fixer\PhpUnit\PhpUnitTestCaseStaticMethodCallsFixer;
use PhpCsFixer\Fixer\PhpUnit\PhpUnitTestClassRequiresCoversFixer;
use PhpCsFixer\Fixer\Semicolon\MultilineWhitespaceBeforeSemicolonsFixer;
use PhpCsFixer\Fixer\Whitespace\NoExtraBlankLinesFixer;
use Symfony\Component\DependencyInjection\Loader\Configurator\ContainerConfigurator;
use Symplify\EasyCodingStandard\ValueObject\Option;
use Symplify\EasyCodingStandard\ValueObject\Set\SetList;

return static function (ContainerConfigurator $configurator): void {
    $parameters = $configurator->parameters();
    $parameters->set(Option::PATHS, [
        __DIR__ . '/src',
    ]);
    $parameters->set(Option::PARALLEL, true);

    $services = $configurator->services();

    // Import base rules
    $configurator->import(SetList::PHP_CS_FIXER);

    // We do not use @covers annotations
    $services->remove(PhpUnitTestClassRequiresCoversFixer::class);

    // @see https://github.com/FriendsOfPHP/PHP-CS-Fixer/issues/4446
    $services->set(PhpdocToCommentFixer::class)->call('configure', [
        [
            'ignored_tags' => ['psalm-suppress', 'var', 'psalm-var']
        ]
    ]);

    $services->set(MultilineWhitespaceBeforeSemicolonsFixer::class)->call('configure', [
        [
            'strategy' => 'no_multi_line'
        ]
    ]);

    $services->set(PhpUnitMethodCasingFixer::class)->call('configure', [
        [
            'case' => 'snake_case'
        ]
    ]);

    $services->set(PhpdocTypesOrderFixer::class)->call('configure', [
        [
            'null_adjustment' => 'always_last'
        ]
    ]);

    $services->set(NoExtraBlankLinesFixer::class)->call('configure', [
        [
            'tokens' => [
                'break',
                'case',
                'continue',
                'curly_brace_block',
                'default',
                'extra',
                'parenthesis_brace_block',
                'return',
                'square_brace_block',
                'switch',
                'throw',
//                'use', Allow blank line in use statement for separation between functions/consts/classes
                'use_trait'
            ]
        ]
    ]);

    // Import base rules
    $configurator->import(SetList::PHP_CS_FIXER_RISKY);

    // Dont rename test methods
    $services->remove(PhpUnitTestAnnotationFixer::class);
    // We want to import functions
    $services->remove(NativeFunctionInvocationFixer::class);
    // This breaks assertions that compare object equality but not reference.
    $services->remove(PhpUnitStrictFixer::class);
    $services->remove(SelfAccessorFixer::class);

    $services->set(PhpUnitTestCaseStaticMethodCallsFixer::class)->call('configure', [
        ['call_type' => 'this']
    ]);

    $configurator->import(SetList::PSR_12);
    $configurator->import(SetList::SPACES);
    $configurator->import(SetList::ARRAY);
    $configurator->import(SetList::DOCBLOCK);
    $configurator->import(SetList::CLEAN_CODE);
    $configurator->import(SetList::NAMESPACES);
    $configurator->import(SetList::STRICT);
    $configurator->import(SetList::COMMENTS);

    $services->set(OrderedImportsFixer::class)->call('configure', [
        [
            'sort_algorithm' => 'alpha',
            'imports_order' => [
                'class',
                'function',
                'const',
            ]
        ]
    ]);


//    // Allow @throws annotation.
//    $services->set(GeneralPhpdocAnnotationRemoveFixer::class)->call('configure', [
//            [
//                'annotations' => ['author', 'package', 'group', 'covers', 'since']
//            ]
//        ]
//    );


};
