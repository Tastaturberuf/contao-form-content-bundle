<?php

declare(strict_types=1);

namespace Tastaturberuf\ContaoFormContentBundle\EventListener\DataContainer;


use Contao\ContentModel;
use Contao\CoreBundle\DataContainer\PaletteManipulator;
use Contao\CoreBundle\ServiceAnnotation\Hook;
use Contao\FormModel;


class FormDataContainer
{

    public const ONETIME = 1;
    public const SESSION = 2;


    public function __construct()
    {
    }


    /**
     * @Hook("initializeSystem")
     */
    public function initializeContaoConfig(): void
    {
        // allow content table in form backend module
        $GLOBALS['BE_MOD']['content']['form']['tables'][] = ContentModel::getTable();
    }


    /**
     * @Hook("loadDataContainer")
     */
    public function loadDataContainer(string $table): void
    {
        if ( $table === FormModel::getTable() )
        {

            $GLOBALS['TL_DCA'][$table] = \array_merge_recursive(
                (array) $GLOBALS['TL_DCA'][$table],
                $this->getDcaConfig($table)
            );

            $this->configPalettes($table);
        }
    }


    private function getDcaConfig(string $table): array
    {
        return
        [
            'list' =>
            [
                'operations' =>
                [
                    'edit_content' =>
                    [
                        'href' => 'table='.ContentModel::getTable(),
                        'icon' => 'articles.svg'
                    ]
                ]
            ],
            'palettes' =>
            [
                '__selector__' => ['fc_show_content']
            ],
            'subpalettes' =>
            [
                'fc_show_content' => 'fc_mode'
            ],
            'fields' =>
            [
                'fc_show_content' =>
                [
                    'inputType' => 'checkbox',
                    'eval'      =>
                    [
                        'submitOnChange' => true,
                        'tl_class'       => 'w50 m12'
                    ],
                    'sql' => "char(1) NOT NULL default ''"
                ],
                'fc_mode' =>
                [
                    'inputType' => 'select',
                    'options'   =>
                    [
                        self::ONETIME => 'onetime',
                        self::SESSION => 'session'
                    ],
                    'reference' => &$GLOBALS['TL_LANG'][$table]['fc_mode_options'],
                    'eval'      =>
                    [
                        'tl_class' => 'w50'
                    ],
                    'sql' => "tinyint(1) unsigned NOT NULL default '1'"
                ]
            ]
        ];
    }


    private function configPalettes(string $table): void
    {
        PaletteManipulator::create()
            ->addLegend('form_content_legend', 'store_legend', PaletteManipulator::POSITION_APPEND)
            ->addField('fc_show_content', 'form_content_legend', PaletteManipulator::POSITION_AFTER)
            ->applyToPalette('default', $table)
        ;
    }

}