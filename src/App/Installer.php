<?php
declare(strict_types=1);

namespace Technoquill\Framework\App;


use RuntimeException;

final class Installer
{
    /**
     * @var array
     */
    private static array $postInstallFolders = [
        'Http',
        'Http/Controllers',
        'Http/Middlewares',
        'Entity',
        'Repositories',
        'Providers',
        'Services',
    ];

    /**
     * Creates a set of predefined folders if they do not already exist.
     * Each folder is created with a permission mode of 0755.
     * If a folder cannot be created, an exception is thrown.
     * Use composer.json for script post-create-project-cmd
     *
     * @return void
     */
    public static function postInstall(): void
    {
        foreach (self::$postInstallFolders as $folder) {
            $folder = APP_BASE_PATH . DIRECTORY_SEPARATOR . 'app' . DIRECTORY_SEPARATOR . $folder;
            if (!is_dir($folder)) {
                if (!mkdir($folder, 0755, true) && !is_dir($folder)) {
                    throw new RuntimeException(sprintf('Directory "%s" was not created', $folder));
                }
                echo "Created: $folder\n";
            }
            $gitKeep = rtrim($folder, '/') . '/.gitkeep';
            if (!file_exists($gitKeep)) {
                file_put_contents($gitKeep, '');
                echo "Created: $gitKeep\n";
            }
        }
    }


}