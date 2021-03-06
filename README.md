<p align="center"><a href="https://dev.piedweb.com" rel="dofollow">
<img src="https://raw.githubusercontent.com/PiedWeb/piedweb-devoluix-theme/master/src/img/logo_title.png" width="200" height="200" alt="PHP Packages Open Source" />
</a></p>

# CMS & Static Website Generator

[![Latest Version](https://img.shields.io/github/tag/piedweb/cms.svg?style=flat&label=release)](https://github.com/PiedWeb/CMS/tags)
[![Software License](https://img.shields.io/badge/license-MIT-brightgreen.svg?style=flat)](LICENSE)
[![Build Status](https://img.shields.io/travis/PiedWeb/CMS/master.svg?style=flat)](https://travis-ci.org/PiedWeb/CMS)
[![Quality Score](https://img.shields.io/scrutinizer/g/piedweb/cms.svg?style=flat)](https://scrutinizer-ci.com/g/piedweb/cms)
[![Code Coverage](https://img.shields.io/scrutinizer/coverage/g/PiedWeb/CMS.svg?style=flat)](https://scrutinizer-ci.com/g/PiedWeb/CMS/code-structure)
[![Total Downloads](https://img.shields.io/packagist/dt/piedweb/cms-bundle.svg?style=flat)](https://packagist.org/packages/piedweb/cms-bundle)

Yet ! Another CMS :)

PiedWebCMS is a Symfony Bundle built with traits.

It permit to get the basic functionalities for **a website focus on content for the Web**... to go further or to use as it.

A Static Website Generator (compatible with Github Pages, Apache Server, ...) is integrated in his core (and could be easily desintegrated).

Build on top on [Symfony 4](https://github.com/symfony/symfony), [VichUploader](https://github.com/dustin10/VichUploaderBundle), [LiipImagine](https://github.com/liip/LiipImagineBundle), [Sonata Admin](https://github.com/sonata-project/SonataAdminBundle), and more (see [composer.json](https://github.com/PiedWeb/CMS/blob/master/composer.json)).

**If you install it, be careful, the first version is not out and the code is not heavily tested !**

## Installation

Supposing composer is installed globally

```
curl https://raw.githubusercontent.com/PiedWeb/CMS/master/install-cms >> install-cms && chmod +x install-cms && ./install-cms ./my-folder
# Valid 'a' to install all recipes !
```

Else, look at [`install-cms`](https://raw.githubusercontent.com/PiedWeb/CMS/master/install-cms) where each step is describe.

Then edit `config/packages/piedweb_cms.yaml` and files in `assets` to configure your web app. Default web app is stored in sqlite.

## Update

Check [Update.md](https://raw.githubusercontent.com/PiedWeb/CMS/master/UPDATE.md)

## Test

... todo

## Documentation

Have a look to the [cookbook](https://github.com/PiedWeb/CMS/blob/master/docs/Cookbook.md).

## Extension

List of Bundles wich extend this one:

- [Conversation](https://packagist.org/packages/piedweb/conversation)

## TODO

- [ ] Move media to var (and create a link ?!)
- [ ] Intégrer LinksImprover (+ UX), après précédent
- [ ] PageScannerService : check external ressource (see todo)
- [ ] search for all "todo" in the code, refactor and test the code (see `composer require illuminate/collections`)
- [ ] reduce duplicate code between StaticService and Controllers
- [ ] export/import FLAT FILES (spatie/yaml-front-matter, vérif à chaque requête pour une sync constante admin <-> flat files)
- [ ] Release V1
- [ ] Look for a better writer experience (https://github.com/front/g-editor)
- [ ] Create a page from a Media (media edit)
- [ ] Multi upload
- [ ] Intégrer Schema.org dans le backend d'une page
- [ ] Better management for social network from backend (plugin ?!)
- [ ] Scan : scanner une page en direct + scanner plus de choses (liens externes, texte alternative manquant, etc.)
- [ ] Gérer un système d'extension viable pour l'admin : à l'install, créer les fichiers Admin qui étendent l'admin de base
      L'ajout d'un plugin modifie automatiquement ce nouveau fichier en ajoutant le code nécessaire (ajout d'une trait + édition d'une fonction)

## Credits

- [PiedWeb](https://piedweb.com)
- [All Contributors](https://github.com/PiedWeb/CMS/graphs/contributors)

## License

The MIT License (MIT). Please see [License File](LICENSE) for more information.

[![Latest Version](https://img.shields.io/github/tag/piedweb/cms.svg?style=flat&label=release)](https://github.com/PiedWeb/CMS/tags)
[![Software License](https://img.shields.io/badge/license-MIT-brightgreen.svg?style=flat)](LICENSE)
[![Build Status](https://img.shields.io/travis/PiedWeb/CMS/master.svg?style=flat)](https://travis-ci.org/PiedWeb/CMS)
[![Quality Score](https://img.shields.io/scrutinizer/g/piedweb/cms.svg?style=flat)](https://scrutinizer-ci.com/g/piedweb/cms)
[![Code Coverage](https://img.shields.io/scrutinizer/coverage/g/PiedWeb/CMS.svg?style=flat)](https://scrutinizer-ci.com/g/PiedWeb/CMS/code-structure)
[![Total Downloads](https://img.shields.io/packagist/dt/piedweb/cms-bundle.svg?style=flat)](https://packagist.org/packages/piedweb/cms-bundle)
