<?php

namespace PiedWeb\CMSBundle\DependencyInjection;

use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\ConfigurationInterface;

class Configuration implements ConfigurationInterface
{
    /**
     * php bin/console config:dump-reference PiedWebCMSBundle.
     */
    public function getConfigTreeBuilder()
    {
        $treeBuilder = new TreeBuilder();
        $rootNode = $treeBuilder
            ->root('piedweb_cms')
                ->children()
                    ->scalarNode('media_dir_absolute')->defaultValue('%kernel.project_dir%/media')->cannotBeEmpty()->end() // NOT USED ??
                    ->scalarNode('locale')->defaultValue('%locale%')->cannotBeEmpty()->end()
                    ->scalarNode('locales')->defaultValue('fr|en')->end()
                    ->scalarNode('dir')->defaultValue('%kernel.project_dir%/public')->cannotBeEmpty()->end() // not explicit = public dir/web dir
                    ->scalarNode('name')->defaultValue('Pied Web CMS')->end()
                    ->scalarNode('color')->defaultValue('#1fa67a')->end()
                    ->scalarNode('default_page_template')->defaultValue('@PiedWebCMS/page/page.html.twig')->cannotBeEmpty()->end()
                    ->scalarNode('entity_page')->defaultValue('PiedWeb\CMSBundle\Entity\Page')->cannotBeEmpty()->end()
                    ->scalarNode('entity_media')->defaultValue('PiedWeb\CMSBundle\Entity\Media')->cannotBeEmpty()->end()
                    ->scalarNode('entity_user')->defaultValue('PiedWeb\CMSBundle\Entity\User')->cannotBeEmpty()->end()
                    // For Fos User and maybe other bundle
                    ->scalarNode('email_sender')->defaultValue('me@tld.com')->cannotBeEmpty()->end()
                    ->scalarNode('email_sender_name')->defaultValue('PiedWebCMS')->cannotBeEmpty()->end()
                ->end()
        ;

        return $treeBuilder;
    }
}