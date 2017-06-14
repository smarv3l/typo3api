<?php

if (!defined('TYPO3_MODE')) {
    die('Access denied.');
}

call_user_func(function () {
    /** @var \TYPO3\CMS\Extbase\SignalSlot\Dispatcher $signalSlotDispatcher */
    $dispatcherClass = \TYPO3\CMS\Extbase\SignalSlot\Dispatcher::class;
    $signalSlotDispatcher = \TYPO3\CMS\Core\Utility\GeneralUtility::makeInstance($dispatcherClass);
    $signalSlotDispatcher->connect(
        \TYPO3\CMS\Install\Service\SqlExpectedSchemaService::class,
        'tablesDefinitionIsBeingBuilt',
        \Mp\MpTypo3Api\Hook\SqlSchemaHook::class,
        'modifyTablesDefinitionString'
    );
});