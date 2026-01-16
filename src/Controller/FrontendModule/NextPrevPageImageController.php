<?php

declare(strict_types=1);

namespace Swoopde\ContaoNextPrevPageimageBundle\Controller\FrontendModule;

use Contao\BackendTemplate;
use Contao\CoreBundle\DependencyInjection\Attribute\AsFrontendModule;
use Contao\CoreBundle\Framework\ContaoFramework;
use Contao\FilesModel;
use Contao\FrontendTemplate;
use Contao\ModuleModel;
use Contao\PageModel;
use Contao\StringUtil;
use Contao\Template;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\HttpFoundation\Response;

#[AsFrontendModule(
    name: 'next_prev_pageimage',
    category: 'navigationMenu',
    template: 'mod_next_prev_pageimage',
    type: 'next_prev_pageimage'
)]
class NextPrevPageImageController
{
    public function __construct(
        private readonly ContaoFramework $framework,
        private readonly RequestStack $requestStack,
    ) {}

    public function __invoke(ModuleModel $model, string $section, array $classes = null): Response
    {
        $this->framework->initialize();

        static $cssLoaded=false;
        if (!$cssLoaded && ($model->np_loadCss ?? '1') === '1') {
            //👉 Der Name contaonextprevpageimage kommt von Bundle-Namen
            //(ContaoNextPrevPageimageBundle → lowercase, ohne „Bundle“)
            //https://deine-domain.tld/bundles/contaonextprevpageimage/css/next_prev_pageimage.css
            $GLOBALS['TL_CSS'][] = 'bundles/contaonextprevpageimage/css/next_prev_pageimage.css|static';
            $cssLoaded = true;
        }

        /** @var PageModel|null $objPage */
        global $objPage;

        /*
         * ==========================================================
         * Backend / CLI / Warmup → Wildcard
         * ==========================================================
         */
        if (!$objPage instanceof PageModel) {
            $bt = new BackendTemplate('be_wildcard');
            $bt->wildcard = '### NEXT / PREV (PAGEIMAGE) ###';

//            $headline = StringUtil::deserialize($model->headline, true);
//            $bt->title = ($headline['value'] ?? '') !== '' ? (string) $headline['value'] : (string) $model->name;
            $bt->title = '';

            return new Response($bt->parse());
        }

        /*
         * ==========================================================
         * Frontend
         * ==========================================================
         */

        $tpl = new FrontendTemplate('mod_next_prev_pageimage');
        $tpl->setData($model->row());

        /*
         * 1) Parent-Ebene bestimmen
         */
        $parentId  = (int) $objPage->pid;
        $currentId = (int) $objPage->id;

        // Root gesetzt → passende Ebene ermitteln
        if (!empty($model->np_rootPage)) {
//            error_log('np_rootPage');
            $rootId    = (int) $model->np_rootPage;
            $candidate = $objPage;

            // Nach oben laufen, bis direkt unter Root
            while (
                $candidate instanceof PageModel
                && (int) $candidate->pid !== 0
                && (int) $candidate->pid !== $rootId
            ) {
                $candidate = PageModel::findById((int) $candidate->pid);
            }

            if ($candidate instanceof PageModel && (int) $candidate->pid === $rootId) {
                $parentId  = $rootId;
                $currentId = (int) $candidate->id;
            }
        }

//        error_log('step1');
        /*
         * 2) Seiten laden (korrekt veröffentlicht)
         */
        $pages = PageModel::findPublishedByPid($parentId, ['order' => 'sorting ASC']);

        if ($pages === null) {
            $tpl->prev = null;
            $tpl->next = null;
            return new Response($tpl->parse());
        }

//        error_log('step2');
        /*
         * 3) Optional: nur Navigationsseiten
         */
        $list = [];

        foreach ($pages as $p) {
            if (!empty($model->np_onlyNav) && (bool) ($p->hide ?? false)) {
                continue;
            }

            $list[] = $p;
        }
//        error_log('list.count='. count($list));

//        error_log('step3');
        /*
         * 4) Aktuelle Seite in Liste finden
         */
        $currentIndex = null;

        foreach ($list as $i => $p) {
            if ((int) $p->id === $currentId) {
                $currentIndex = $i;
                break;
            }
        }

//        error_log('step4');
        if ($currentIndex === null) {
            $tpl->prev = null;
            $tpl->next = null;
            return new Response($tpl->parse());
        }

//        error_log('step5');
        /*
         * 5) Prev / Next bestimmen
         */
        $prev = $list[$currentIndex - 1] ?? null;
        $next = $list[$currentIndex + 1] ?? null;

        $tpl->prev = $prev ? $this->buildLinkData($prev, $model) : null;
        $tpl->next = $next ? $this->buildLinkData($next, $model) : null;

        return new Response($tpl->parse());
    }

    private function buildLinkData(PageModel $page, ModuleModel $model): array
    {
        $title = (string) ($page->pageTitle ?: $page->title);

        $data = [
            'href'  => $page->getAbsoluteUrl(),
            'title' => $title,
        ];

        if (!empty($model->np_showImage)) {
            $file = $this->getFirstPageImage($page);

            if ($file) {
                $data['image'] = [
                    'from' => $file->uuid, // figure() kann UUID
                    'alt'  => $title,
                    'size' => $this->normalizeSize($model->np_imageSize ?? null),
                ];
            }
        }

        return $data;
    }

    /**
     * terminal42/contao-pageimage: Feld "pageImage" enthält serialisierte UUIDs.
     */
    private function getFirstPageImage(PageModel $page): ?FilesModel
    {
        if (!isset($page->pageImage) || empty($page->pageImage)) {
            return null;
        }

        $uuids = StringUtil::deserialize($page->pageImage, true);
        if (empty($uuids)) {
            return null;
        }

        return FilesModel::findByUuid($uuids[0]);
    }

    private function normalizeSize(mixed $raw): mixed
    {
        if (empty($raw)) {
            return null;
        }

        $size = StringUtil::deserialize($raw, true);

        if (is_array($size)) {
            $size = array_values(array_map(static function ($v) {
                return is_string($v) ? trim($v) : $v;
            }, $size));

            // komplett leer?
            if (count(array_filter($size, static fn($v) => $v !== '' && $v !== null)) === 0) {
                return null;
            }

            // Spezialfall: nur eine Bildgroessen-ID wurde gewaehlt -> ["", "", "6"]
            if (
                count($size) >= 3
                && ($size[0] === '' || $size[0] === null)
                && ($size[1] === '' || $size[1] === null)
                && is_numeric($size[2])
            ) {
                return [0, 0, (int) $size[2]];
            }

            // Strings "0" etc. sauber casten (optional, aber hilfreich)
            if (isset($size[0]) && is_numeric($size[0])) $size[0] = (int) $size[0];
            if (isset($size[1]) && is_numeric($size[1])) $size[1] = (int) $size[1];
            if (isset($size[2]) && is_numeric($size[2])) $size[2] = (int) $size[2];

            return $size;
        }

        if (is_string($raw) && trim($raw) !== '') {
            return $raw;
        }

        return null;
    }


}
