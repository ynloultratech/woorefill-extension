<?php

/**
 * Created by PhpStorm.
 * User: Rafael
 * Date: 29/11/2016
 * Time: 09:50 AM
 */

namespace WooRefill\SandBox;

/**
 * Class NamespacePrefixHelper
 */
class NamespacePrefixHelper
{
    /**
     * Prefix the usage of given namespace updating the content of given
     *
     * @param string $prefix        Prefix to add to given namespace
     * @param string $namespace     namespace
     * @param string &$usageContext content of the class when need update the namespace
     *
     * @return integer will be return the number of replacements done.
     */
    public static function prefixUsage($prefix, $namespace, &$usageContext)
    {
        $namespace = preg_replace('/\\\\?$/', '', $namespace);//remove trailing

        if (count(explode('\\', $namespace)) > 1) {
            $namespace = str_replace('\\', '\\\\\\\?', $namespace);
        }

        $regexs = [
            "/([^\\w\\d\\\\])(\\\\$namespace)/" => "$1\\\\$prefix$2", // starting with \ -> \SomeNamespace
            "/([^\\w\\d\\\\'\"])($namespace)([\s;\"'\\\\])/" => "$1$prefix\\\\$2$3", // //normal -> SomeNamespace
            "/([^\\w\\d\\\\])($namespace)(\\\\\\\\)/" => "$1$prefix$3$2$3", //usage with double in strings -> 'SomeNamespace\\Class'
            "/(['\"])($namespace)(\\\\)/" => "$1$prefix\\\\$2$3", //as is in string -> 'SomeNamespace\Class'
        ];

        $counters = 0;
        foreach ($regexs as $regex => $replacement) {
            $count = 0;
            $usageContext = preg_replace($regex, $replacement, $usageContext, -1, $count);
            $counters += $count;
        }

        return $counters;
    }

    /**
     * makeNameSpaceReplacement
     *
     * @param $prefix
     * @param $namespace
     *
     * @return string
     */
    private static function makeNameSpaceReplacement($prefix, $namespace)
    {
        $namespaces = explode('\\', $namespace);
        $replace = "$1$2$3$prefix$4";
        $last = 1 + (count($namespaces) * 2);
        $count = 2;
        while ($count <= $last) {
            $replace .= "$$count";
            $count++;
        }
        echo $replace."\n";
        exit;

        return $replace;
    }

    /**
     * makeNameSpaceRegex
     *
     * @param $prefix
     * @param $namespace
     *
     * @return string
     */
    private static function makeNameSpaceRegex($prefix, $namespace)
    {
        $namespaces = explode('\\', $namespace);
        $regex = "([^($prefix\\\\)])(['\\\"\\s\\n])(\\\\?)(";
        foreach ($namespaces as $index => $name) {
            if ($index + 1 < count($namespaces)) {
                $regex .= "$name\\\\\\\?";
            } else {
                $regex .= "$name\\\\?\\\?)";
            }
        }

        return "/$regex/";
    }
}