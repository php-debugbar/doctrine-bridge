<?php
/*
 * This file is part of the DebugBar package.
 *
 * (c) 2013 Maxime Bouroumeau-Fuseau
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace DebugBar\Bridge\Doctrine;

use DebugBar\DataCollector\AssetProvider;
use DebugBar\DataCollector\DataCollector;
use DebugBar\DataCollector\Renderable;
use DebugBar\DebugBarException;
use Doctrine\DBAL\Logging\DebugStack;
use Doctrine\ORM\EntityManager;

/**
 * Collects Doctrine queries
 *
 * http://doctrine-project.org
 *
 * Uses the DebugStack logger to collects data about queries
 *
 * <code>
 * $debugStack = new Doctrine\DBAL\Logging\DebugStack();
 * $entityManager->getConnection()->getConfiguration()->setSQLLogger($debugStack);
 * $debugbar->addCollector(new DoctrineCollector($debugStack));
 * </code>
 */
class DoctrineCollector extends DataCollector implements Renderable, AssetProvider
{
    protected $debugStack;		
    
    protected $durationBackground = false;

    /**
     * DoctrineCollector constructor.
     * @param $debugStackOrEntityManager
     * @throws DebugBarException
     */
    public function __construct($debugStackOrEntityManager)
    {
        if ($debugStackOrEntityManager instanceof EntityManager) {
            $debugStackOrEntityManager = $debugStackOrEntityManager->getConnection()->getConfiguration()->getSQLLogger();
        }
        if (!($debugStackOrEntityManager instanceof DebugStack)) {
            throw new DebugBarException("'DoctrineCollector' requires an 'EntityManager' or 'DebugStack' object");
        }
        $this->debugStack = $debugStackOrEntityManager;
    }

    /**
     * Enable/disable the shaded duration background on queries
     *
     * @param  bool $enabled
     */
    public function setDurationBackground($enabled)
    {
        $this->durationBackground = $enabled;
    }

    /**
     * @return array
     */
    public function collect()
    {
        $queries = array();
        $nb_statements = 0;
        $totalExecTime = 0;
        $transactions = ['START TRANSACTION', 'SAVEPOINT', 'COMMIT', 'RELEASE SAVEPOINT', 'ROLLBACK', 'ROLLBACK TO SAVEPOINT'];
        foreach ($this->debugStack->queries as $q) {
            if (in_array(trim($q['sql'] , '" '), $transactions)) {
                $q['type'] =  'transaction';
            }

            $queries[] = array(
                'sql' => $q['sql'],
                'params' => (object) $this->getParameters($q['params'] ?? []),
                'duration' => $q['executionMS'],
                'duration_str' => $this->formatDuration($q['executionMS']),
                'type' => $q['type'] ?? null,
            );
            $totalExecTime += $q['executionMS'];
            if (($q['type'] ?? null) === 'transaction') continue;
            $nb_statements++;
        }

        if ($this->durationBackground && $totalExecTime > 0) {
            // For showing background measure on Queries tab
            $start_percent = 0;
            foreach ($queries as $i => $stmt) {
                if (!isset($stmt['duration'])) {
                    continue;
                }
                $width_percent = $stmt['duration'] / $totalExecTime * 100;
                $queries[$i] = array_merge($stmt, [
                    'start_percent' => round($start_percent, 3),
                    'width_percent' => round($width_percent, 3),
                ]);
                $start_percent += $width_percent;
            }
        }

        return array(
            'count' => count($queries),
            'nb_statements' => $nb_statements,
            'accumulated_duration' => $totalExecTime,
            'accumulated_duration_str' => $this->formatDuration($totalExecTime),
            'statements' => $queries
        );
    }

    /**
     * Returns an array of parameters used with the query
     *
     * @return array
     */
    public function getParameters($params) : array
    {
        return array_map(function ($param) {
            if (is_string($param)) {
                return htmlentities($param, ENT_QUOTES, 'UTF-8', false);
            } elseif (is_array($param)) {
                return '[' . implode(', ', $this->getParameters($param)) . ']';
            } elseif (is_numeric($param)) {
                return strval($param);
            } elseif ($param instanceof \DateTimeInterface) {
                return $param->format('Y-m-d H:i:s');
            } elseif (is_object($param)) {
                return json_encode($param);
            }
            return $param ?: '';
        }, $params);
    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'doctrine';
    }

    /**
     * @return array
     */
    public function getWidgets()
    {
        return array(
            "database" => array(
                "icon" => "arrow-right",
                "widget" => "PhpDebugBar.Widgets.SQLQueriesWidget",
                "map" => "doctrine",
                "default" => "[]"
            ),
            "database:badge" => array(
                "map" => "doctrine.nb_statements",
                "default" => 0
            )
        );
    }

    /**
     * @return array
     */
    public function getAssets()
    {
        return array(
            'css' => 'widgets/sqlqueries/widget.css',
            'js' => 'widgets/sqlqueries/widget.js'
        );
    }
}
