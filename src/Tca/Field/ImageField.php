<?php
/**
 * Created by PhpStorm.
 * User: marco
 * Date: 18.06.17
 * Time: 13:17
 */

namespace Typo3Api\Tca\Field;


use Symfony\Component\OptionsResolver\Options;
use Symfony\Component\OptionsResolver\OptionsResolver;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use Typo3Api\Utility\DbFieldDefinition;

class ImageField extends FileField
{
    /**
     * I only want to allow sane formats which can be tested and are somewhat reasonable and stable
     *
     * svg support in typo3 is basically none existent ~ you should do intense testing if you want svg's
     * ai support is also broken ~ entirely
     * pcx and tga are too obscure so i dropped them
     * pdf is like pandora's box ... with memory leaks, timeouts etc.
     * bmp files tend to be huge ~ you shouldn't accept those
     */
    const BLACKLISTED_FORMATS = ['svg', 'ai', 'pcx', 'tga', 'pdf', 'bmp'];

    protected function configureOptions(OptionsResolver $resolver)
    {
        parent::configureOptions($resolver);
        $resolver->setDefaults([
            'allowedFileExtensions' => array_diff(
                GeneralUtility::trimExplode(',', strtolower($GLOBALS['TYPO3_CONF_VARS']['GFX']['imagefile_ext'])),
                ImageField::BLACKLISTED_FORMATS
            ),
            'cropVariants' => null,
        ]);

        $resolver->addAllowedTypes('cropVariants', ['array', 'null']);
        $resolver->setNormalizer('cropVariants', function (Options $options, $cropVariants) {
            if ($cropVariants === null) {
                return null;
            }

            $parsedCropVariants = [];
            foreach ($cropVariants as $name => $aspectRatios) {
                if (is_string($aspectRatios)) {
                    $aspectRatios = GeneralUtility::trimExplode(',', $aspectRatios, true);
                }

                $allowedAspectRatios = [];
                foreach ($aspectRatios as $aspectRatio) {
                    if ($aspectRatio === 'NaN') {
                        $allowedAspectRatios['NaN'] = [
                            'title' => 'LLL:EXT:lang/Resources/Private/Language/locallang_wizards.xlf:imwizard.ratio.free',
                            'value' => 0.0
                        ];
                        continue;
                    }

                    $parts = GeneralUtility::trimExplode(':', $aspectRatio, true);
                    if (count($parts) !== 2) {
                        $msg = "Aspect ratio $aspectRatio could not be parsed. Expected something like 16:9.";
                        throw new \RuntimeException($msg);
                    }

                    $x = floatval($parts[0]);
                    $y = floatval($parts[1]);
                    if ($x <= 0 || $y <= 0) {
                        $msg = "Aspect ratio $aspectRatio did not return usable sizes, got $x and $y.";
                        throw new \RuntimeException($msg);
                    }

                    $allowedAspectRatios[$aspectRatio] = [
                        'title' => $aspectRatio,
                        'value' => $x / $y
                    ];
                }

                $parsedCropVariants[$name] = [
                    'title' => $name,
                    'allowedAspectRatios' => $allowedAspectRatios
                ];
            }

            return $parsedCropVariants;
        });
    }

    public function getFieldTcaConfig(string $tableName)
    {
        $config = parent::getFieldTcaConfig($tableName);

        // copy the column overrides from the image type in tt_content
        // i don't want to copy paste all that definition stuff
        $config['overrideChildTca']['types'] = $GLOBALS['TCA']['tt_content']['columns']['image']['config']['overrideChildTca']['types'];
        $config['appearance'] = array_merge(
            $GLOBALS['TCA']['tt_content']['columns']['image']['config']['appearance'],
            $config['appearance']
        );

        $config['overrideChildTca']['columns']['crop']['config']['cropVariants'] = $this->getOption('cropVariants');

        return $config;
    }
}