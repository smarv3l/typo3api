<?php
/**
 * Created by PhpStorm.
 * User: marco
 * Date: 28.06.17
 * Time: 11:10
 */

namespace Typo3Api\Tca\Field;


use Symfony\Component\OptionsResolver\OptionsResolver;

class MediaField extends FileField
{
    protected function configureOptions(OptionsResolver $resolver)
    {
        parent::configureOptions($resolver);
        $resolver->setDefaults([
            'allowedFileExtensions' => $GLOBALS['TYPO3_CONF_VARS']['SYS']['mediafile_ext']
        ]);
    }

    public function getFieldTcaConfig(string $tableName)
    {
        $config = parent::getFieldTcaConfig($tableName);

        // copy the column overrides from the image type in tt_content
        // i don't want to copy paste all that definition stuff
        $config['overrideChildTca']['types'] = $GLOBALS['TCA']['tt_content']['columns']['assets']['config']['overrideChildTca']['types'];
        $config['appearance'] = array_merge(
            $GLOBALS['TCA']['tt_content']['columns']['assets']['config']['appearance'],
            $config['appearance']
        );

        return $config;
    }
}