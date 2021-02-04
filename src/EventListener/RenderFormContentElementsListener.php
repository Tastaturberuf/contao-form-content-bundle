<?php


declare(strict_types=1);


namespace Tastaturberuf\ContaoFormContentBundle\EventListener;


use Contao\ContentModel;
use Contao\Controller;
use Contao\CoreBundle\ServiceAnnotation\Hook;
use Contao\Form;
use Contao\FormModel;
use Contao\ModuleModel;
use Contao\System;

use Tastaturberuf\ContaoFormContentBundle\EventListener\DataContainer\FormDataContainer;


class RenderFormContentElementsListener
{

    /**
     * @var array
     */
    private $formSession;


    public function __construct()
    {
        if ( PHP_!key_exists('FORM_CONTENT_SUBMITTED_FORMS', (array) $_SESSION) )
        {
            $_SESSION['FORM_CONTENT_SUBMITTED_FORMS'] = [];
        }

        $this->formSession = &$_SESSION['FORM_CONTENT_SUBMITTED_FORMS'];
    }


    /**
     * @Hook("processFormData")
     */
    public function processFormData(
        array $submittedData,
        array $formData,
        ?array $files,
        array $labels,
        Form $form
    ): void
    {
        $this->formSession[$form->id] = $form->id;
    }


    /**
     * @Hook("getContentElement")
     */
    public function getContentElement(ContentModel $contentModel, string $buffer, object $element): string
    {
        if ( $element instanceof Form )
        {
            return $this->getForm($element->getModel(), $buffer);
        }

        if ( 'module' === $element->type )
        {
            $moduleModel = ModuleModel::findByPk($element->module);

            if ( $moduleModel->type === 'form' )
            {
                $formModel = FormModel::findByPk($moduleModel->form);

                return $this->getForm($formModel, $buffer);
            }
        }

        return $buffer;
    }


    /**
     * @Hook("getFrontendModule")
     */
    public function getFrontendModule(ModuleModel $moduleModel, string $buffer, object $module): string
    {
        if ( $module instanceof Form )
        {
            return $this->getForm($module->getModel(), $buffer);
        }

        return $buffer;
    }


    /**
     * @Hook("getForm")
     */
    public function getForm(FormModel $form, string $buffer): string
    {
        if ( $form->fc_show_content && in_array($form->id, $this->formSession) )
        {
            // unset id if mode is one time
            if ( $form->fc_mode == FormDataContainer::ONETIME )
            {
                unset($this->formSession[$form->id]);
            }

            return $this->getContentElements($form->id);
        }

        return $buffer;
    }


    /**
     * @param int|string $formId
     */
    private function getContentElements($formId): string
    {
        $formId = (int) $formId;

        $buffer = '';

        if ( $elements = ContentModel::findPublishedByPidAndTable($formId, FormModel::getTable()) )
        {

            foreach ($elements as $element)
            {
                $buffer .= Controller::getContentElement($element);
            }
        }
        else
        {
            System::log("Form id {$formId} has no content elements.", __METHOD__, TL_FORMS);
        }

        return $buffer;
    }

}
