<?php

/*
 * LICENSE: This file is subject to the terms and conditions defined in
 * file 'LICENSE', which is part of this source code package.
 *
 * @copyright 2017 Copyright(c) - All rights reserved.
 *
 * @author YNLO-Ultratech Development Team <developer@ynloultratech.com>
 * @package woorefill-extension
 * @version 1.0.x
 */

namespace WooRefill\SandBox;

use Composer\Autoload\AutoloadGenerator;
use Composer\IO\IOInterface;
use Composer\Package\RootPackageInterface;
use Composer\Script\Event;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\Finder\Finder;
use Symfony\Component\Finder\SplFileInfo;

/**
 * Class ScriptHandler
 */
class ScriptHandler
{
    /**
     * bury
     */
    public static function sandbox(Event $event)
    {
        $extras = $event->getComposer()->getPackage()->getExtra();

        if (!isset($extras['sandbox'])) {
            throw new \InvalidArgumentException('Sandbox need to be configured through the extra.sandbox setting.');
        }

        if (!isset($extras['sandbox']['prefix'])) {
            throw new \InvalidArgumentException('Sandbox need to be configured through the extra.sandbox setting.');
        }

        $vendorDir = $event->getComposer()->getConfig()->get('vendor-dir');
        $prefix = $extras['sandbox']['prefix'];

        $io = $event->getIO();

        $io->write('Resolving composer namespaces...');

        /** @var RootPackageInterface $package */
        $package = $event->getComposer()->getPackage();
        $ignoreNamespaces = [];
        if (isset($package->getAutoload()['psr-4'])) {
            foreach ($package->getAutoload()['psr-4'] as $namespace => $path) {
                $ignoreNamespaces[] = $namespace;
            }
        }
        if (isset($package->getDevAutoload()['psr-4'])) {
            foreach ($package->getDevAutoload()['psr-4'] as $namespace => $path) {
                $ignoreNamespaces[] = $namespace;
            }
        }

        $namespaces = self::resolveNamespaces($vendorDir, $prefix, $ignoreNamespaces);

        $io->write(sprintf('[OK] %s namespaces found', count($namespaces)));

        $finder = new Finder();
        $finder->in($vendorDir);
        if (isset($extras['sandbox']['name'])) {
            $names = (array)$extras['sandbox']['name'];
        } else {
            $names = ['*.php', '*.json'];
        }

        foreach ($names as $name) {
            $finder->name($name);
        }

        $io->write('Prefixing namespaces...');
        $processed = 0;
        $updated = 0;
        $changes = 0;
        foreach ($finder->files() as $file) {
            $content = $file->getContents();
            $processed++;
            foreach ($namespaces as $namespace) {
                if ($replacements = NamespacePrefixHelper::prefixUsage($prefix, $namespace, $content)) {
                    file_put_contents($file->getPathname(), $content);
                    $updated++;
                    $changes += $replacements;
                }
            }
        }

        $io->write(sprintf('[%s] Files processed', $processed));
        $io->write(sprintf('[%s] Files updated', $updated));
        $io->write(sprintf('[%s] Changes', $changes));

        if (isset($extras['sandbox']['clean'])) {
            $io->write('Cleaning up files and directories.');
            self::clean($vendorDir, $extras['sandbox']['clean']);
        }

        $io->write('[OK] Success.');
    }

    /**
     * @param string $vendorDir
     * @param string $prefix
     * @param array  $ignore
     *
     * @return array
     */
    protected static function resolveNamespaces($vendorDir, $prefix, $ignore = [])
    {
        $namespaces = [];
        $psr4NameSpaces = include realpath($vendorDir.'/composer/autoload_psr4.php');
        foreach ($psr4NameSpaces as $namespace => $paths) {
            if (in_array($namespace, $ignore)) {
                continue;
            }

            $namespace = preg_replace("/$prefix\\\\?/", '', $namespace);
            if (preg_match('/\\\\$/', $namespace)) {
                $namespaces[] = $namespace;
            }
        }

        return $namespaces;
    }

    /**
     * clean
     *
     * @param $vendorDir
     * @param $config
     */
    protected static function clean($vendorDir, $config)
    {
        $excludes = isset($config['exclude']) ? $config['exclude'] : [];
        $dirs = isset($config['dirs']) ? $config['dirs'] : [];
        $files = isset($config['files']) ? $config['files'] : [];

        $fileSystem = new Filesystem();
        $dirsToRemove = self::resolveFilesToRemove($vendorDir, $dirs, $excludes, true);
        foreach ($dirsToRemove as $dir) {
            $fileSystem->remove(realpath($vendorDir.'/'.$dir));
        }

        $filesToRemove = self::resolveFilesToRemove($vendorDir, $files, $excludes);
        foreach ($filesToRemove as $file) {
            $fileSystem->remove(realpath($vendorDir.'/'.$file));
        }
    }

    /**
     * resolveFilesToRemove
     *
     * @param string $vendorDir
     * @param string $patterns
     * @param string $excludes
     * @param bool   $onlyDirectories resolve only directories, otherwise resolve only files
     *
     * @return array
     */
    protected static function resolveFilesToRemove($vendorDir, $patterns, $excludes, $onlyDirectories = false)
    {
        $toRemove = [];
        foreach ($patterns as $pattern) {
            $finder = new Finder();
            $finder->in($vendorDir);
            $finder->ignoreDotFiles(false);
            $path = pathinfo($pattern, PATHINFO_DIRNAME);
            if ($path !== '.') {
                $finder->path($path);
            }
            $name = pathinfo($pattern, PATHINFO_FILENAME);
            if ($name && $name !== '*') {
                $finder->name($name);
            } else {
                $finder->name($pattern);
            }
            if ($onlyDirectories) {
                foreach ($finder->directories() as $directory) {
                    if (!self::isExcluded($directory->getRelativePathname(), $excludes)) {
                        $toRemove[] = $directory->getRelativePathname();
                    }
                }
            } else {
                foreach ($finder->files() as $file) {
                    if (!self::isExcluded($file->getRelativePathname(), $excludes)) {
                        $toRemove[] = $file->getRelativePathname();
                    }
                }
            }
        }

        return $toRemove;
    }

    /**
     * isExcluded
     *
     * @param string $relativePathName
     * @param array  $excludes
     *
     * @return bool
     */
    private static function isExcluded($relativePathName, $excludes)
    {
        foreach ($excludes as $exclude) {
            $exclude = str_replace('/', DIRECTORY_SEPARATOR, $exclude);
            if ($exclude === $relativePathName) {
                return true;
            }
        }

        return false;
    }
}