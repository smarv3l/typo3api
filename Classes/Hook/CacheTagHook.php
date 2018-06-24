<?php

namespace Typo3Api\Hook;


use TYPO3\CMS\Core\Cache\CacheManager;
use TYPO3\CMS\Core\Utility\GeneralUtility;

class CacheTagHook
{
    public static function attach()
    {
        $GLOBALS['TYPO3_CONF_VARS']['SC_OPTIONS']['t3lib/class.t3lib_tcemain.php']['clearCachePostProc']['typo3api'] = static::class . '->clearCachePostProcess';
        // TODO check if the event can also be attached to extbase to somehow clear the cache after persistAll
    }

    /**
     * @param array $params
     *
     * @throws \TYPO3\CMS\Core\Cache\Exception\NoSuchCacheGroupException
     */
    public function clearCachePostProcess(array $params)
    {
        if (!isset($GLOBALS['TCA'][$params['table']]['ctrl']['EXT']['typo3api']['cache_tags'])) {
            return;
        }

        $cacheManager = GeneralUtility::makeInstance(CacheManager::class);

        foreach ($GLOBALS['TCA'][$params['table']]['ctrl']['EXT']['typo3api']['cache_tags'] as $group => $tags) {
            foreach ($tags as &$tag) {
                $tag = str_replace(
                    ['###UID###', '###PID###'],
                    [$params['uid'], $params['uid_page']],
                    $tag
                );
            }

            $cacheManager->flushCachesInGroupByTags($group, $tags);
        }
    }
}