<?php

namespace PiedWeb\CMSBundle\Service;

use Doctrine\ORM\EntityManagerInterface;
use PiedWeb\CMSBundle\Entity\PageInterface as Page;
use PiedWeb\CMSBundle\Repository\PageRepository;
use PiedWeb\CMSBundle\Service\PageCanonicalService as PageCanonical;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Contracts\Translation\TranslatorInterface;
use Twig\Environment as Twig;
use WyriHaximus\HtmlCompress\Factory as HtmlCompressor;
use WyriHaximus\HtmlCompress\Parser as HtmlCompressorParser;

class StaticService
{
    /**
     * Contain files relative to SEO wich will be hard copied.
     *
     * @var array
     */
    protected $robotsFiles = ['robots.txt', 'feed.xml', 'sitemap.xml', 'sitemap.txt'];

    /**
     * @var array
     */
    protected $dontCopy = ['index.php', '.htaccess'];

    /**
     * @var EntityManagerInterface
     */
    protected $em;

    /**
     * @var Filesystem
     */
    protected $filesystem;

    /**
     * @var Twig
     */
    protected $twig;

    /**
     * @var string
     */
    protected $webDir;

    /**
     * @var string
     */
    protected $staticDir;

    /**
     * @var RequestStack
     */
    protected $requesStack;

    /**
     * @var \PiedWeb\CMSBundle\Service\PageCanonicalService
     */
    protected $pageCanonical;

    /**
     * @var TranslatorInterface
     */
    protected $translator;

    /**
     * @var HtmlCompressorParser
     */
    protected $parser;

    /**
     * @var ParameterBagInterface
     */
    protected $params;

    /**
     * Used in .htaccess generation.
     *
     * @var string
     */
    protected $redirections = '';

    public function __construct(
        EntityManagerInterface $em,
        Twig $twig,
        ParameterBagInterface $params,
        RequestStack $requesStack,
        PageCanonical $pageCanonical,
        TranslatorInterface $translator,
        string $webDir
    ) {
        $this->em = $em;
        $this->filesystem = new Filesystem();
        $this->twig = $twig;
        $this->params = $params;
        $this->requesStack = $requesStack;
        $this->webDir = $webDir;
        $this->pageCanonical = $pageCanonical;
        $this->translator = $translator;
        $this->staticDir = $this->params->get('pwc.static.dir');
        $this->parser = HtmlCompressor::construct();
    }

    /**
     * Main Logic is here.
     *
     * @throws \RuntimeException
     * @throws \LogicException
     */
    public function dump()
    {
        if (!method_exists($this->filesystem, 'dumpFile')) {
            throw new \RuntimeException('Method dumpFile() is not available. Upgrade your Filesystem.');
        }

        $this->filesystem->remove($this->staticDir);

        if ($this->params->get('pwc.static.generateForApache')) {
            $this->generateHtaccess();
            $symlink = $this->params->get('pwc.static.symlinkMedia');
        } else { //if ($this->params->has('pwc.static.generateForGithubPages')) {
            // symlink doesn't work on github page.
            $symlink = false;
            $this->generateCname();
        }

        $this->generatePages();
        $this->generateErrorPages();
        $this->copyRobotsFiles();
        $this->copyAssets($symlink);
        $this->copyMediaToDownload($symlink);
    }

    /**
     * Copy files relative to SEO (robots, sitemaps, etc.).
     */
    protected function copyRobotsFiles(): void
    {
        array_map([$this, 'copy'], $this->robotsFiles);
    }

    // todo
    // docs
    // https://help.github.com/en/github/working-with-github-pages/managing-a-custom-domain-for-your-github-pages-site
    protected function generateCname()
    {
        $this->filesystem->dumpFile($this->staticDir.'/CNAME', $this->params->get('pwc.static.domain'));
    }

    protected function generateHtaccess()
    {
        if (!$this->params->has('pwc.static.domain')) {
            throw new \Exception('Before, you need to configure (in config/packages/piedweb_cms.yaml) static_domain.');
        }

        $htaccess = $this->twig->render('@PiedWebCMS/static/htaccess.twig', [
            'domain' => $this->params->get('pwc.static.domain'),
            'redirections' => $this->redirections,
        ]);
        $this->filesystem->dumpFile($this->staticDir.'/.htaccess', $htaccess);
    }

    protected function copy(string $file): void
    {
        if (file_exists($file)) {
            copy(
                str_replace($this->params->get('kernel.project_dir').'/', '../', $this->webDir.'/'.$file),
                $this->staticDir.'/'.$file
            );
        }
    }

    /**
     * Copy (or symlink) for all assets in public
     * (and media previously generated by liip in public).
     */
    protected function copyAssets(bool $symlink = true): void
    {
        $dir = dir($this->webDir);
        while (false !== $entry = $dir->read()) {
            if ('.' == $entry || '..' == $entry) {
                continue;
            }
            if (!in_array($entry, $this->robotsFiles) && !in_array($entry, $this->dontCopy)) {
                //$this->symlink(
                if (true === $symlink) {
                    $this->filesystem->symlink(
                        str_replace($this->params->get('kernel.project_dir').'/', '../', $this->webDir.'/'.$entry),
                        $this->staticDir.'/'.$entry
                    );
                } else {
                    $action = is_file($this->webDir.'/'.$entry) ? 'copy' : 'mirror';
                    $this->filesystem->$action($this->webDir.'/'.$entry, $this->staticDir.'/'.$entry);
                }
            }
        }
        $dir->close();
    }

    /**
     * Copy or Symlink "not image" media to download folder.
     *
     * @return void
     */
    protected function copyMediaToDownload(bool $symlink = true)
    {
        if (!file_exists($this->staticDir.'/download')) {
            $this->filesystem->mkdir($this->staticDir.'/download/');
            $this->filesystem->mkdir($this->staticDir.'/download/media');
        }

        $dir = dir($this->webDir.'/../media');
        while (false !== $entry = $dir->read()) {
            if ('.' == $entry || '..' == $entry) {
                continue;
            }
            // if the file is an image, it's ever exist (maybe it's slow to check every files)
            if (!file_exists($this->webDir.'/media/default/'.$entry)) {
                if (true === $symlink) {
                    $this->filesystem->symlink('../../../media/'.$entry, $this->staticDir.'/download/media/'.$entry);
                } else {
                    $this->filesystem->copy(
                        $this->webDir.'/../media/'.$entry,
                        $this->staticDir.'/download/media/'.$entry
                    );
                }
            }
        }

        //$this->filesystem->$action($this->webDir.'/../media', $this->staticDir.'/download/media');
    }

    protected function generatePages(): void
    {
        $pages = $this->getPages();

        foreach ($pages as $page) {
            $this->generatePage($page);
            $this->generateFeedFor($page);
        }
    }

    /**
     * The function cache redirection found during generatePages and
     * format in self::$redirection the content for the .htaccess.
     *
     * @return void
     */
    protected function addRedirection(Page $page)
    {
        $this->redirections .= 'Redirect ';
        $this->redirections .= $page->getRedirectionCode().' ';
        $this->redirections .= $this->pageCanonical->generatePathForPage($page->getRealSlug());
        $this->redirections .= ' '.$page->getRedirection();
        $this->redirections .= PHP_EOL;
    }

    public function generatePage(Page $page)
    {
        // set current locale to avoid twig error
        $request = new Request();
        $request->setLocale($page->getLocale());
        $this->requesStack->push($request);

        //$this->translator->setLocale($page->getLocale());

        // check if it's a redirection
        if (false !== $page->getRedirection()) {
            $this->addRedirection($page);

            return;
        }

        $dump = $this->render($page);
        $this->filesystem->dumpFile($this->getFilePath($page), $dump);
    }

    protected function getFilePath(Page $page)
    {
        $slug = '' == $page->getRealSlug() ? 'index' : $page->getRealSlug();
        $route = $this->pageCanonical->generatePathForPage($slug);

        return $this->staticDir.$route.'.html';
    }

    /**
     * Generate static file for feed indexing children pages
     * (only if children pages exists).
     *
     * @return void
     */
    protected function generateFeedFor(Page $page)
    {
        if ($page->getChildrenPages()->count() > 0) {
            $dump = $this->renderFeed($page);
            $this->filesystem->dumpFile(preg_replace('/.html$/', '.xml', $this->getFilePath($page)), $dump);
        }
    }

    protected function generateErrorPages(): void
    {
        $this->generateErrorPage();

        // todo i18n error in .htaccess
        $locales = explode('|', $this->params->get('pwc.locales'));

        foreach ($locales as $locale) {
            $this->filesystem->mkdir($this->staticDir.'/'.$locale);
            $this->generateErrorPage($locale);
        }
    }

    protected function generateErrorPage($locale = null, $uri = '404.html')
    {
        if (null !== $locale) {
            $request = new Request();
            $request->setLocale($locale);
            $this->requesStack->push($request);
        }

        $dump = $this->parser->compress($this->twig->render('@Twig/Exception/error.html.twig'));
        $this->filesystem->dumpFile($this->staticDir.(null !== $locale ? '/'.$locale : '').'/'.$uri, $dump);
    }

    protected function getPageRepository(): PageRepository
    {
        return $this->em->getRepository($this->params->get('pwc.entity_page'));
    }

    protected function getPages()
    {
        $query = $this->getPageRepository()->getQueryToFindPublished('p');

        return $query->getQuery()->getResult();
    }

    protected function render(Page $page): string
    {
        $template = $this->params->get('pwc.default_page_template');

        return $this->parser->compress($this->twig->render($template, ['page' => $page]));
    }

    // todo i18n feed ...
    protected function renderFeed(Page $page): string
    {
        $template = '@PiedWebCMS/page/rss.xml.twig';

        return $this->parser->compress($this->twig->render($template, ['page' => $page]));
    }
}
