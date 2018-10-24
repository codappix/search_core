<?php

namespace Codappix\SearchCore\Utility;

/**
 * Utility: Array
 * @package Codappix\SearchCore\Utility
 */
class ArrayUtility
{

    /**
     * Recursively removes empty array elements.
     *
     * @param array $array
     * @return array the modified array
     * @see \TYPO3\CMS\Extbase\Utility\ArrayUtility::removeEmptyElementsRecursively Removed in TYPO3 v9
     */
    public static function removeEmptyElementsRecursively(array $array): array
    {
        $result = $array;
        foreach ($result as $key => $value) {
            if (is_array($value)) {
                $result[$key] = self::removeEmptyElementsRecursively($value);
                if ($result[$key] === []) {
                    unset($result[$key]);
                }
            } elseif ($value === null) {
                unset($result[$key]);
            }
        }
        return $result;
    }

}
