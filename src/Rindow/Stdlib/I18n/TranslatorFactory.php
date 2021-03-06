<?php
namespace Rindow\Stdlib\I18n;

class TranslatorFactory
{
    public static function factory($serviceManager)
    {
        $config = $serviceManager->get('config');
        $translator = new Translator();
        if(isset($config['translator'])) {
            $config = $config['translator'];

            if(isset($config['translation_file_patterns'])) {
                foreach ($config['translation_file_patterns'] as $pattern) {
                    if(!isset($pattern['type']) || strtolower($pattern['type'])!='gettext')
                        continue;
                    if(!isset($pattern['text_domain']) || !isset($pattern['base_dir']))
                        continue;
                    $translator->bindTextDomain($pattern['text_domain'],$pattern['base_dir']);
                }
            }
        }
        return $translator;
    }
}