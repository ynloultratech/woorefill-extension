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
     * @param string        $prefix        Prefix to add to given namespace
     * @param NamespaceMeta $namespaceMeta namespace
     * @param string        &$usageContext content of the class when need update the namespace
     *
     * @return integer will be return the number of replacements done.
     */
    public static function prefixUsage($prefix, NamespaceMeta $namespaceMeta, &$usageContext)
    {
        //$namespace = preg_replace('/\\\\?$/', '', $namespaceMeta->getName());//remove trailing
        $namespace = $namespaceMeta->getName();
        //prepare namespace for regex
        if (count(explode('\\', $namespace)) > 1) {
            $namespace = str_replace('\\', '\\\?\\\\?', $namespace);
        }

        $underscoredNamespace = false;
        if (count(explode('_', $namespace)) > 1) {
            $underscoredNamespace = true;
        }

        $regexs =[
            "/([^$prefix])($namespace)/" => "$1$prefix$2"
        ];

//        switch ($namespaceMeta->getPsr()) {
//            case 0:
//                $replacement = $underscoredNamespace ? '_' : '\\\\';
//
//                $regexs = [
//                    "/([^\\w\\d\\\\])(\\\\$namespace)/" => "$1$replacement$prefix$2", // starting with \ -> \SomeNamespace
//                    "/([^\\w\\d\\\\'\"])($namespace)([\s;\"'\\\\])/" => "$1$prefix$replacement$2$3", // //normal -> SomeNamespace
//                    "/(['\"])($namespace)(['\"])/" => "$1$prefix$replacement$2$3", // //use as is in string, commonly in autoloader .. 'PhpCollection'
//                    "/(\(\s*)($namespace)(\s+)/" => "$1$prefix$replacement$2$3", // //using class as function parameter (Twig $twig)
//                    "/([^\\w\\d\\\\])($namespace)(\\\\\\\\)/" => "$1$prefix$replacement$2$3", //usage with double in strings -> 'SomeNamespace\\Class'
//                    "/(['\"])($namespace)(\\\\)/" => "$1$prefix$replacement$2$3", //as is in string -> 'SomeNamespace\Class'
//                ];
//
//                if ($underscoredNamespace && preg_match('/_$/', $namespace)) {
//                    $namespaceWithout_ = preg_replace('/_$/', null, $namespace);
//                    $regexs["/([\s\"'\\\\])($namespaceWithout_)(_[\w+\"'])/"] = "$1$prefix$replacement$2$3"; // //wordAsPrefix -> Twig_
//                    $regexs["/(\(\s*)($namespaceWithout_)(_\w+)/"] = "$1$prefix$replacement$2$3"; // //using class as function parameter (Twig_Interface $twig)
//                }
//                break;
//            case 4:
//                $regexs = [
//                    "/([^\\w\\d\\\\])(\\\\$namespace)/" => "$1\\\\$prefix$2", // starting with \ -> \SomeNamespace
//                    "/([^\\w\\d\\\\'\"])($namespace)([\s;\"'\\\\])/" => "$1$prefix\\\\$2$3", // //normal -> SomeNamespace
//                    "/([^\\w\\d\\\\])($namespace)(\\\\\\\\)/" => "$1$prefix$3$2$3", //usage with double in strings -> 'SomeNamespace\\Class'
//                    "/(['\"])($namespace)(\\\\)/" => "$1$prefix\\\\$2$3", //as is in string -> 'SomeNamespace\Class'
//                ];
//                break;
//            default:
//                $regexs = [];
//        }


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