<?php

namespace Typo3Api\Tca\Field;


use TYPO3\CMS\Core\Resource\File;
use TYPO3\CMS\Core\Utility\ExtensionManagementUtility;
use Typo3Api\PreparationForTypo3;

class ImageFieldTest extends FileFieldTest
{
    use PreparationForTypo3; // tt_content is needed here

    protected function createFieldInstance(string $name, array $options = []): AbstractField
    {
        // require 'vendor/typo3/cms/typo3/sysext/core/Configuration/DefaultConfiguration.php';
        $GLOBALS['TYPO3_CONF_VARS']['GFX']['imagefile_ext'] = 'gif,jpg,jpeg,tif,tiff,bmp,pcx,tga,png,pdf,ai,svg';
        return new ImageField($name, $options);
    }

    protected function assertBasicCtrlChange(AbstractField $field)
    {
        $ctrl = [];
        $field->modifyCtrl($ctrl, 'stub_table');
        $this->assertEquals([
            'thumbnail' => $field->getName()
        ], $ctrl, "ctrl change");
    }

    protected function assertBasicColumns(AbstractField $field)
    {
        $this->assertEquals([
            $field->getName() => [
                'label' => $field->getOption('label'),
                'config' => ExtensionManagementUtility::getFileFieldTCAConfig($field->getName(), [
                    'minitems' => 0,
                    'maxitems' => 100,
                    'appearance' => [
                        'collapseAll' => true,
                        'createNewRelationLinkTitle' => 'LLL:EXT:frontend/Resources/Private/Language/locallang_ttc.xlf:images.addFileReference'
                    ],
                    'overrideChildTca' => [
                        'types' => [
                            '0' => [
                                'showitem' => '
                                --palette--;LLL:EXT:lang/Resources/Private/Language/locallang_tca.xlf:sys_file_reference.imageoverlayPalette;imageoverlayPalette,
                                --palette--;;filePalette'
                            ],
                            File::FILETYPE_TEXT => [
                                'showitem' => '
                                --palette--;LLL:EXT:lang/Resources/Private/Language/locallang_tca.xlf:sys_file_reference.imageoverlayPalette;imageoverlayPalette,
                                --palette--;;filePalette'
                            ],
                            File::FILETYPE_IMAGE => [
                                'showitem' => '
                                --palette--;LLL:EXT:lang/Resources/Private/Language/locallang_tca.xlf:sys_file_reference.imageoverlayPalette;imageoverlayPalette,
                                --palette--;;filePalette'
                            ],
                            File::FILETYPE_AUDIO => [
                                'showitem' => '
                                --palette--;LLL:EXT:lang/Resources/Private/Language/locallang_tca.xlf:sys_file_reference.audioOverlayPalette;audioOverlayPalette,
                                --palette--;;filePalette'
                            ],
                            File::FILETYPE_VIDEO => [
                                'showitem' => '
                                --palette--;LLL:EXT:lang/Resources/Private/Language/locallang_tca.xlf:sys_file_reference.videoOverlayPalette;videoOverlayPalette,
                                --palette--;;filePalette'
                            ],
                            File::FILETYPE_APPLICATION => [
                                'showitem' => '
                                --palette--;LLL:EXT:lang/Resources/Private/Language/locallang_tca.xlf:sys_file_reference.imageoverlayPalette;imageoverlayPalette,
                                --palette--;;filePalette'
                            ]
                        ]
                    ]
                ], 'gif,jpg,jpeg,tif,tiff,png')
            ]
        ], $field->getColumns('stub_table'));
    }

    /**
     * @dataProvider validNameProvider
     * @param string $fieldName
     */
    public function testThumbnail(string $fieldName)
    {
        $altFieldName = $fieldName . '_2';

        $ctrl = [];
        $field = $this->createFieldInstance($fieldName, ['useAsThumbnail' => false]);
        $field->modifyCtrl($ctrl, 'stub_table1');
        $this->assertEmpty($ctrl, "No thumbnail modified");

        $ctrl = [];
        $field = $this->createFieldInstance($fieldName, ['useAsThumbnail' => true]);
        $field->modifyCtrl($ctrl, 'stub_table1');
        $this->assertEquals(['thumbnail' => $fieldName], $ctrl, "thumbnail added");

        $ctrl = [];
        $field = $this->createFieldInstance($fieldName);
        $field->modifyCtrl($ctrl, 'stub_table1');
        $this->assertEquals(['thumbnail' => $fieldName], $ctrl, "thumbnail added even if not specified");

        // $ctrl = []; // left out on purpose
        $field = $this->createFieldInstance($altFieldName);
        $field->modifyCtrl($ctrl, 'stub_table1');
        $this->assertEquals(['thumbnail' => $fieldName], $ctrl, "thumbnail not overwritten");

        // $ctrl = []; // left out on purpose
        $field = $this->createFieldInstance($altFieldName, ['useAsThumbnail' => 'force']);
        $field->modifyCtrl($ctrl, 'stub_table1');
        $this->assertEquals(['thumbnail' => $altFieldName], $ctrl, "thumbnail force overwritten");


    }
}
