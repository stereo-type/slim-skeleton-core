<?php

namespace App\Core\Lib;

use FilesystemIterator;
use RecursiveIteratorIterator;
use ReflectionClass;
use SplFileInfo;
use Symfony\Component\Finder\Iterator\RecursiveDirectoryIterator;

class Files
{
    /**
     * @param string $dir
     * @param bool $ucfirst
     * @return ReflectionClass[]
     */
    public static function dir_classes(string $dir, bool $ucfirst = true): array
    {
        $root = realpath(ROOT_PATH);

        $iterator = new RecursiveIteratorIterator(
            new RecursiveDirectoryIterator($dir, FilesystemIterator::SKIP_DOTS),
            RecursiveIteratorIterator::SELF_FIRST
        );
        $classes = [];
        foreach ($iterator as $file) {
            if ($file->isFile()) {
                $namespace = str_replace('/', '\\', substr($file->getPath(), strlen($root) + 1));
                $class = pathinfo($file->getFilename(), PATHINFO_FILENAME);
                $fullClassName = ($ucfirst ? ucfirst($namespace) : $namespace) . '\\' . $class;
                if (class_exists($fullClassName)) {
                    $reflectionClass = new ReflectionClass($fullClassName);
                    $classes[$fullClassName] = $reflectionClass;
                }
            }
        }
        return $classes;
    }


    /**
     * @param string $directory
     * @param string $subpath
     * @return string[]
     */
    public static function getPathsRecursively(string $directory, string $subpath): array
    {
        $iterator = new RecursiveIteratorIterator(
            new RecursiveDirectoryIterator($directory, FilesystemIterator::SKIP_DOTS),
            RecursiveIteratorIterator::SELF_FIRST
        );

        $result = [];
        foreach ($iterator as $path) {
            if ($path->isDir()) {
                $pathArray = explode('/', $path->getPathname());
                if (strtolower(end($pathArray)) === strtolower($subpath)) {
                    $result[] = $path->getRealPath();
                }
            }
        }
        return $result;
    }

    /**
     * @param string $directory
     * @param string $subpath
     * @param string $fileName
     * @param string[] $exclude
     * @return array
     */
    public static function findFiles(string $directory, string $subpath, string $fileName, array $exclude = []): array
    {
        $paths = self::getPathsRecursively($directory, $subpath);
        $files = [];
        foreach ($paths as $path) {
            $iterator = new RecursiveIteratorIterator(
                new RecursiveDirectoryIterator($path, FilesystemIterator::SKIP_DOTS),
                RecursiveIteratorIterator::SELF_FIRST
            );


            foreach ($iterator as $file) {
                if (($file instanceof SplFileInfo) && $file->isFile() && $file->getFilename() === $fileName) {
                    $p = $file->getRealPath();
                    if (!in_array($p, $exclude, true)) {
                        $files [] = $p;
                    }
                }
            }
        }

        return $files;
    }

    public static function listFromFiles(string $directory, string $subpath, string $fileName, array $exclude = []): array
    {
        $result = [];
        foreach (self::findFiles($directory, $subpath, $fileName, $exclude) as $c) {
            $result = array_merge($result, require $c);
        }
        return $result;
    }

}
