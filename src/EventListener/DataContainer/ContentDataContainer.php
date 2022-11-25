<?php


declare(strict_types=1);


namespace Tastaturberuf\ContaoFormContentBundle\EventListener\DataContainer;


use Contao\ContentModel;
use Contao\CoreBundle\ServiceAnnotation\Hook;
use Contao\FormModel;


class ContentDataContainer
{

    /**
     * @Hook("loadDataContainer")
     */
    public function loadDataContainer(string $table): void
    {
        if ( ('form' === $_GET['do'] ?? null) && $table === ContentModel::getTable() )
        {
            // set dynamic parent table name
            $GLOBALS['TL_DCA'][$table]['config']['ptable'] = FormModel::getTable();

            // set mode 4 form module header fields
            $GLOBALS['TL_DCA'][$table]['list']['sorting']['headerFields'] = array_merge(
                $GLOBALS['TL_DCA'][$table]['list']['sorting']['headerFields'],
                ['fc_show_content', 'fc_mode']
            );
        }
    }

}
