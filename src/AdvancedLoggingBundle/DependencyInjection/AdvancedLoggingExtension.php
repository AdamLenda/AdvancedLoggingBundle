<?php
namespace AdvancedLoggingBundle\DependencyInjection;

use AdvancedLoggingBundle\AdvancedLoggingBundle;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Definition;
use Symfony\Component\DependencyInjection\Loader\YamlFileLoader;
use Symfony\Component\DependencyInjection\Reference;
use Symfony\Component\HttpKernel\DependencyInjection\Extension;
use Symfony\Component\DependencyInjection\Exception\InvalidArgumentException;

/**
 * Class LogExtension
 * @package AdvancedLoggingBundle\DependencyInjection
 */
class AdvancedLoggingExtension extends Extension
{
    /**
     * Loads a specific configuration.
     *
     * @param array            $configs   An array of configuration values
     * @param ContainerBuilder $container A ContainerBuilder instance
     *
     * @throws \InvalidArgumentException When provided tag is not defined in this extension
     */
    public function load(array $configs, ContainerBuilder $container)
    {
        $configuration = new Configuration();

        if (empty($configs) || empty($configs[0])) {
            /*
             * First we load the files that contain the configuration information we need
             */
            $kernelRootDir = $container->getParameter('kernel.root_dir');
            $kernelEnvironment = $container->getParameter('kernel.environment');
            $environmentConfigDirectoryPaths[] = $kernelRootDir.'/config/'.$kernelEnvironment;
            $environmentConfigDirectoryPaths[] = $kernelRootDir.'/config';

            $container->registerExtension($this);
            $loader = new YamlFileLoader(
                $container,
                new FileLocator(
                    $environmentConfigDirectoryPaths
                )
            );
            $loader->load('advancedlogging.yml');
            $configs = $container->getExtensionConfig('advanced_logging');
        }
        $LogConfigArray = $this->processConfiguration($configuration, $configs);

        if (!is_array($LogConfigArray) || empty($LogConfigArray)) {
            throw new \InvalidArgumentException('Unable to located configuration for Log Bundle');
        }

        if (!array_key_exists('log_writers', $LogConfigArray)) {
            throw new \InvalidArgumentException('Did not find "log_writers" array in Log Bundle configuration');
        }

        $LogWritersConfig = $LogConfigArray['log_writers'];
        if (empty($LogWritersConfig)) {
            throw new \InvalidArgumentException('Log Bundle configuration for "log_writers" array is empty');
        }

        /*
         * Define the log writer services
         */
        $LogServiceDefinition = new Definition('AdvancedLoggingBundle\Service\LogService');

        foreach ($LogWritersConfig as $LogWriterName => $LogWriterConfig) {
            if (!array_key_exists('class', $LogWriterConfig) || empty($LogWriterConfig['class'])) {
                throw new \InvalidArgumentException(
                    'Log Bundle configuration for log writer "'.$LogWriterName.'" is missing "class" value'
                );
            }

            if (array_key_exists('arguments', $LogWriterConfig) && !empty($LogWriterConfig['arguments'])) {
                $arguments = [];
                foreach ($LogWriterConfig['arguments'] as $key => $value) {
                    if (strpos($value, '@') === 0) {
                        $arguments[$key] = new Reference(substr($value, 1));
                    } else {
                        $arguments[$key] = $value;
                    }
                }
            } else {
                $arguments = null;
            }
            $LogWriterDefinition = new Definition($LogWriterConfig['class'], $arguments);
//            if (array_key_exists('calls', $LogWriterConfig) && !empty($LogWriterConfig['calls'])) {
//                foreach ($LogWriterConfig['calls'] as $call) {
//                    $method = $call[0];
//                    if (array_key_exists(1, $call)) {
//                        $callArguments = $call[1];
//                    } else {
//                        $callArguments = null;
//                    }
//                    $LogWriterDefinition->addMethodCall($method, $callArguments);
//                }
//            }
            $container->setDefinition($LogWriterName, $LogWriterDefinition);

            $LogServiceDefinition->addMethodCall('registerLogger', [new Reference($LogWriterName)]);
        }

        $container->setDefinition('advancedloggingbundle.log_service', $LogServiceDefinition);

//        $LogService = $container->get('advancedloggingbundle.log_service');

        //        $Definition->addMethodCall('registerLogger', [])
        //$logAPIAdapterConfig = $LogConfigArray['advancedloggingbundle.log_api_adapter'];
        //$definition = new Definition($logAPIAdapterConfig['class'], $logAPIAdapterConfig['arguments']);
        //$container->setDefinition('advancedloggingbundle.log_api_adapter', $definition);

//
//        /*
//         * Now define services made available by this bundle
//         */
//        $definition = new Definition(
//            'AdvancedLoggingBundle\Service\LogFormatService',
//            [new Reference('advancedloggingbundle.log_api_adapter')]
//        );
//        $container->setDefinition('advancedloggingbundle.service.log_format_service', $definition);
    }
}