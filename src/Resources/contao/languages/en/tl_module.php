<?php

declare(strict_types=1);

/**
 * Module label
 */
$GLOBALS['TL_LANG']['FMD']['next_prev_pageimage']
    = [
    'Previous/Next navigation with image', 'Displays links to the previous and next page including a page image (pageimage).'];


$GLOBALS['TL_LANG']['tl_module']['nextprev_legend']='Settings and Filter';
//
$GLOBALS['TL_LANG']['tl_module']['np_loadCss'] = ['Load CSS', 'Automatically loads the CSS for the Next/Prev module.'];


/**
 * Field labels
 */
$GLOBALS['TL_LANG']['tl_module']['np_rootPage']
    = [
    'Root page',
    'If set, the children of this page are used instead of the siblings of the current page.'
];

$GLOBALS['TL_LANG']['tl_module']['np_onlyNav']
    = [
    'Navigation pages only',
    'Only consider pages that are visible in the navigation.'
];

$GLOBALS['TL_LANG']['tl_module']['np_showImage']
    = [
    'Show image',
    'Displays the first page image (terminal42/contao-pageimage) in the previous/next link.'
];

$GLOBALS['TL_LANG']['tl_module']['np_imageSize']
    = [
    'Image size',
    'Image size for the previous/next navigation thumbnail.'
];
