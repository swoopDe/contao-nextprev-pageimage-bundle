<?php

declare(strict_types=1);

use Contao\CoreBundle\DataContainer\PaletteManipulator;

//PaletteManipulator::create()
//    ->addLegend('nextprev_legend', 'nav_legend', PaletteManipulator::POSITION_AFTER)
//    ->addField(['np_rootPage', 'np_onlyNav', 'np_showImage', 'np_imageSize'], 'nextprev_legend', PaletteManipulator::POSITION_APPEND)
//    ->applyToPalette('next_prev_pageimage', 'tl_module');
//imgSize
$GLOBALS['TL_DCA']['tl_module']['palettes']['next_prev_pageimage']
    = '{title_legend},name,type;{nextprev_legend},np_rootPage,np_loadCss,np_onlyNav,np_showImage,np_imageSize;{protected_legend:hide},protected;{expert_legend:hide},guests,cssID,space';

// Feld: CSS laden
$GLOBALS['TL_DCA']['tl_module']['fields']['np_loadCss'] = [
    'label'     => &$GLOBALS['TL_LANG']['tl_module']['np_loadCss'],
    'inputType' => 'checkbox',
    'eval'      => ['tl_class' => 'w50 m12'],
    'sql'       => "char(1) NOT NULL default '1'",
];

$GLOBALS['TL_DCA']['tl_module']['fields']['np_rootPage'] = [
    'label'     => &$GLOBALS['TL_LANG']['tl_module']['np_rootPage'],
    'inputType' => 'pageTree',
    'eval'      => ['fieldType' => 'radio', 'tl_class' => 'clr'],
    'sql'       => "int(10) unsigned NOT NULL default 0",
];

$GLOBALS['TL_DCA']['tl_module']['fields']['np_onlyNav'] = [
    'label'     => &$GLOBALS['TL_LANG']['tl_module']['np_onlyNav'],
    'inputType' => 'checkbox',
    'eval'      => ['tl_class' => 'w50 m12'],
    'sql'       => "char(1) NOT NULL default ''",
];

$GLOBALS['TL_DCA']['tl_module']['fields']['np_showImage'] = [
    'label'     => &$GLOBALS['TL_LANG']['tl_module']['np_showImage'],
    'inputType' => 'checkbox',
    'eval'      => ['tl_class' => 'w50 m12'],
    'sql'       => "char(1) NOT NULL default ''",
];

$GLOBALS['TL_DCA']['tl_module']['fields']['np_imageSize'] = [
    'label'     => &$GLOBALS['TL_LANG']['tl_module']['np_imageSize'],
    'exclude'   => true,
    'inputType' => 'imageSize',
    'reference' => &$GLOBALS['TL_LANG']['MSC'],
    // Presets aus tl_image_size laden (wie beim Core-Feld imgSize)
    'options_callback' => static function (): array {
        $sizes = \Contao\System::getContainer()->get('contao.image.sizes');

        // je nach Contao-Version ist die Methode anders benannt
        foreach (['getAllOptions', 'getOptions'] as $method) {
            if (\method_exists($sizes, $method)) {
                $result = $sizes->$method();
                return \is_array($result) ? $result : [];
            }
        }
        return [];
    },
    'eval'      => [
        'includeBlankOption' => true,
        'tl_class' => 'clr',
    ],
    'sql'       => "blob NULL",
];
