<?php

/**
 * Global helper functions, this is a side effect of using Laravel for so long ;).
 */
if (!function_exists('dd')) {
    /**
     * Dump and die.
     *
     * @param mixed $dump
     */
    function dd($dump)
    {
        if (function_exists('dump')) {
            dump($dump);
        } else {
            var_dump($dump);
        }
        die;
    }
}

if (!function_exists('templatePath')) {

    /**
     * @param string $name
     *
     * @return string
     */
    function templatePath($name)
    {
        return APP_BASE.DIRECTORY_SEPARATOR.'views'.DIRECTORY_SEPARATOR.$name;
    }
}

if (!function_exists('getArrayCopy')) {

    /**
     * @param App\Entity\Project|App\Entity\Project[] $records
     * @return array
     */
    function getArrayCopy($records) {
        if (is_array($records)) {
            return array_map(
                function ($record) {
                    /** @var App\Entity\Project $record */
                    return $record->getArrayCopy();
                },
                $records
            );
        }
        return $records->getArrayCopy();
    }
}