<?php

namespace App\EventListener;

use App\Validator\Constraints\CustomConstraints;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\Form\FormError;

class FormValidationListener implements EventSubscriberInterface
{
    public static function getSubscribedEvents()
    {
        return [
            FormEvents::PRE_SUBMIT => 'onPreSubmit',
        ];
    }

    public function onPreSubmit(FormEvent $event)
    {
        $form = $event->getForm();
        $data = $event->getData();
        $validator = $form->getConfig()->getOption('validation_groups');

        if (is_array($data)) {
            foreach ($data as $key => $value) {
                if (is_string($value)) {
                    // Sanitize input
                    $data[$key] = filter_var($value, FILTER_SANITIZE_FULL_SPECIAL_CHARS);

                    // Validate input
                    $violations = $validator->validate($value, new CustomConstraints());
                    if (count($violations) > 0) {
                        foreach ($violations as $violation) {
                            $form->addError(new FormError($violation->getMessage()));
                        }
                    }
                }
            }
        }

        $event->setData($data);
    }
}