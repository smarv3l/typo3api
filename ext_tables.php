<?php
if (!defined('TYPO3_MODE')) {
    die('Access denied.');
}

// some examples on how to use this
//call_user_func(function () use($_EXTKEY) {
//    $teasers = \Mp\MpTypo3Api\Builder\TableBuilder::create($_EXTKEY, 'teaser')
//        ->configure(new \Mp\MpTypo3Api\Tca\LanguageConfiguration())
//        ->configure(new \Mp\MpTypo3Api\Tca\SortingConfiguration())
//        ->configure(new \Mp\MpTypo3Api\Tca\InputField('header'))
//    ;
//
//    $test = \Mp\MpTypo3Api\Builder\TableBuilder::create($_EXTKEY, 'test_table')
//        ->configure(new \Mp\MpTypo3Api\Tca\PageRelationConfiguration())
//        ->configure(new \Mp\MpTypo3Api\Tca\EnableFieldConfiguration())
//        ->configure(new \Mp\MpTypo3Api\Tca\LanguageConfiguration())
//        ->configure(new \Mp\MpTypo3Api\Tca\SortingConfiguration())
//        ->configure(new \Mp\MpTypo3Api\Tca\InputField('name'))
//        ->configure(new \Mp\MpTypo3Api\Tca\SelectField('gender', [
//            'items' => [
//                ['male', 'male'],
//                ['female', 'female'],
//            ]
//        ]))
//        ->configure(new \Mp\MpTypo3Api\Tca\SelectField('age', [
//            'items' => [
//                ['1', '1'],
//                ['2', '2'],
//                ['3', '3'],
//                ['4', '4'],
//            ]
//        ]))
//        ->configure(new \Mp\MpTypo3Api\Tca\IrreField('teasers', [
//            'foreignTable' => $teasers->getName()
//        ]))
//    ;
//
//    \Mp\MpTypo3Api\Builder\TableBuilder::create($_EXTKEY, 'addresse')
//        ->configure(new \Mp\MpTypo3Api\Tca\PageRelationConfiguration(false, true))
//        ->configure(new \Mp\MpTypo3Api\Tca\EnableFieldConfiguration())
//        ->configure(new \Mp\MpTypo3Api\Tca\LanguageConfiguration())
//        ->configure(new \Mp\MpTypo3Api\Tca\SortingConfiguration())
//        ->configure(new \Mp\MpTypo3Api\Tca\InputField('street'))
//        ->configure(new \Mp\MpTypo3Api\Tca\InputField('city'))
//        ->configure(new \Mp\MpTypo3Api\Tca\InputField('zip'))
//    ;
//});
