<?php

if (!defined('TYPO3_MODE')) {
    die('Access denied.');
}

\Typo3Api\Hook\CacheTagHook::attach();
\Typo3Api\Hook\ContentElementWizardHook::attach();
\Typo3Api\Hook\SqlSchemaHook::attach();
