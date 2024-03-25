<?php

namespace App\Core;

use FilesystemIterator;
use RecursiveDirectoryIterator;
use RecursiveIteratorIterator;
use SplFileInfo;

class Utils
{

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
    public static function findFiles(string $directory, string $subpath, string $fileName, array $exclude): array
    {
        $paths = self::getPathsRecursively($directory, $subpath);
        $files = [];
        foreach ($paths as $path) {
            $iterator = new RecursiveIteratorIterator(
                new RecursiveDirectoryIterator($path, FilesystemIterator::SKIP_DOTS),
                RecursiveIteratorIterator::SELF_FIRST
            );


            foreach ($iterator as $file) {
                if ($file instanceof SplFileInfo) {
                    if ($file->isFile() && $file->getFilename() === $fileName) {
                        $p = $file->getRealPath();
                        if (!in_array($p, $exclude)) {
                            $files [] = $p;
                        }
                    }
                }
            }
        }

        return $files;
    }

}