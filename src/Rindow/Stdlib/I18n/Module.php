<?php
namespace Rindow\Stdlib\I18n;

class Module
{
    public function getConfig()
    {
        return array(
            'container' => array(
                'aliases' => array(
                    'I18nMessageTranslator' => 'Rindow\Stdlib\I18n\DefaultTranslator',
                ),
                'components' => array(
                    'Rindow\Stdlib\I18n\DefaultTranslator' => array(
                        'class' => 'Rindow\Stdlib\I18n\Translator',
                        'factory' =>  'Rindow\Stdlib\I18n\TranslatorFactory::factory',
                    ),
                ),
            ),
        );
    }
}
