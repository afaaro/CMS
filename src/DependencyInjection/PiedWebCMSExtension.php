<?php
/**
 * todo: make it cleaner: https://symfony.com/doc/current/bundles/prepend_extension.html.
 */

namespace PiedWeb\CMSBundle\DependencyInjection;

use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Extension\Extension;
use Symfony\Component\DependencyInjection\Extension\PrependExtensionInterface;
use Symfony\Component\DependencyInjection\Loader\YamlFileLoader;
use Symfony\Component\Finder\Finder;
use Symfony\Component\Yaml\Parser;

class PiedWebCMSExtension extends Extension //implements PrependExtensionInterface
{
    public function load(array $configs, ContainerBuilder $container)
    {
        $configuration = new Configuration(); //$configuration = $this->getConfiguration($configs, $container);
        $config = $this->processConfiguration($configuration, $configs);

        self::loadConfigToParameters($container, $config);

        $loader = new YamlFileLoader($container, new FileLocator(__DIR__.'/../Resources/config'));
        $loader->load('services.yaml');

        // todo : https://symfony.com/doc/current/bundles/extension.html#adding-classes-to-compile
    }

    /**
     * @param string $prefix must contain the last
     *
     * @return void
     */
    protected static function loadConfigToParameters(ContainerBuilder $container, array $config, $prefix = '')
    {
        $container->setParameter('pwc', $config);

        foreach ($config as $key => $value) {
            if ('apps' === $key) {
                $container->setParameter('pwc.apps', self::parsAppsConfig($value));
            } elseif (is_array($value)) {
                self::loadConfigToParameters($container, $value, $prefix.$key.'.');
            } else {
                $container->setParameter('app.'.$prefix.$key, $value); // to deprecate in next release
                $container->setParameter('pwc.'.$prefix.$key, $value);
            }
        }
    }

    protected static function parsAppsConfig($apps)
    {
        $result = [];
        foreach ($apps as $app) {
            $result[$app['hosts'][0]] = $app;
        }

        return $result;
    }

    public function getAlias()
    {
        return 'piedweb_cms'; // change to pwc todo
    }

    /*
    public function prepend(ContainerBuilder $container)
    {
        // Load configurations for other package
        $parser = new Parser();
        $finder = Finder::create()->files()->name('*.yaml')->in(__DIR__.'/../Resources/config/packages/');
        foreach ($finder as $file) {
            $configs = $parser->parse(file_get_contents($file->getRealPath()));
            foreach ($configs as $name => $config) {
                $container->prependExtensionConfig($name, $config);
            }
        }
    }
    /**/
}
