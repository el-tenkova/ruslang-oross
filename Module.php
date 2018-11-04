<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/ZendSkeletonApplication for the canonical source repository
 * @copyright Copyright (c) 2005-2015 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */

namespace Application;

use Contents\Model\ContentItem;
use Contents\Model\ContentsTables;

use Contents\Model\Paragraph;
use Contents\Model\ParagraphTables;

use Contents\Model\Rule;
use Contents\Model\RuleTables;

use Contents\Model\FootNote;
use Contents\Model\FootNoteTables;

use Contents\Model\Historic;
use Contents\Model\HistoricTables;

use Contents\Model\Orthogr;
use Contents\Model\OrthogrTables;

use Contents\Model\Formula;
use Contents\Model\FormulaTables;

use Contents\Model\Article;
use Contents\Model\ArticleTables;
use Contents\Model\ArticleFormula;
use Contents\Model\ArticlesFormulasTables;

use Contents\Model\Accent;
use Contents\Model\Word;
use Contents\Model\WordTables;

use Contents\Model\Bigramm;
use Contents\Model\BigrammTables;

use Contents\Model\Trigramm;
use Contents\Model\TrigrammTables;

use Contents\Model\Tetragramm;
use Contents\Model\TetragrammTables;

use Contents\Model\Mistake;
use Contents\Model\MistakeTables;

use Contents\Model\Predisl;
use Contents\Model\PredislTable;

use Contents\Model\DicUser;
use Contents\Model\DicUserTable;

use Contents\Model\State;
use Contents\Model\StateTable;

use Contents\Model\Sources;
use Contents\Model\SourcesTable;

use Contents\Model\ArticleAddInfo;
use Contents\Model\ArticleAddInfoTable;

use Zend\Db\ResultSet\ResultSet;
use Zend\Db\TableGateway\TableGateway;

use Zend\Mvc\ModuleRouteListener;
use Zend\Mvc\MvcEvent;

class Module
{
    public function onBootstrap(MvcEvent $e)
    {
        $eventManager        = $e->getApplication()->getEventManager();
        $moduleRouteListener = new ModuleRouteListener();
        $moduleRouteListener->attach($eventManager);
    }

    public function getConfig()
    {
        return include __DIR__ . '/config/module.config.php';
    }

    public function getAutoloaderConfig()
    {
        return array(
            'Zend\Loader\StandardAutoloader' => array(
                'namespaces' => array(
                    __NAMESPACE__ => __DIR__ . '/src/' . __NAMESPACE__,
                    'Contents' => __DIR__ . '/src/' . 'Contents',
                ),
            ),
        );
    }
    public function getServiceConfig()
    {
        return array(
            'factories' => array(
                'Contents\Model\ContentsTables' =>  function($sm) {
                    $tableGateway = $sm->get('ContentsTableGateway');
                    error_log("create Contents table");
                    $table = new ContentsTables($tableGateway);
                    return $table;
                },
                'ContentsTableGateway' => function ($sm) {
                    $dbAdapter = $sm->get('Zend\Db\Adapter\Adapter');
                    $resultSetPrototype = new ResultSet();
                    $resultSetPrototype->setArrayObjectPrototype(new ContentItem());
                    return new TableGateway(array('p' => 'parts'), $dbAdapter, null, $resultSetPrototype);
                }, 
                'Contents\Model\ParagraphTables' =>  function($sm) {
                    error_log("create Para table");
                    $tableGateway = $sm->get('ParagraphTableGateway');
                    $table = new ParagraphTables($tableGateway);
                    return $table;
                },
                'ParagraphTableGateway' => function ($sm) {
                    $dbAdapter = $sm->get('Zend\Db\Adapter\Adapter');
                    $resultSetPrototype = new ResultSet();
                    $resultSetPrototype->setArrayObjectPrototype(new Paragraph());
                    return new TableGateway(array('p' => 'paras'), $dbAdapter, null, $resultSetPrototype);
                }, 
                'Contents\Model\RuleTables' =>  function($sm) {
                    error_log("create Rule table");
                    $tableGateway = $sm->get('RuleTableGateway');
                    $table = new RuleTables($tableGateway);
                    return $table;
                },
                'FootNoteTableGateway' => function ($sm) {
                    $dbAdapter = $sm->get('Zend\Db\Adapter\Adapter');
                    $resultSetPrototype = new ResultSet();
                    $resultSetPrototype->setArrayObjectPrototype(new FootNote());
                    return new TableGateway(array('f' => 'footnotes'), $dbAdapter, null, $resultSetPrototype);
                }, 
                'Contents\Model\FootNoteTables' =>  function($sm) {
                    error_log("create FootNote table");
                    $tableGateway = $sm->get('FootNoteTableGateway');
                    $table = new FootNoteTables($tableGateway);
                    return $table;
                },
                'HistoricTableGateway' => function ($sm) {
                    $dbAdapter = $sm->get('Zend\Db\Adapter\Adapter');
                    $resultSetPrototype = new ResultSet();
                    $resultSetPrototype->setArrayObjectPrototype(new Historic());
                    return new TableGateway(array('h' => 'historic'), $dbAdapter, null, $resultSetPrototype);
                }, 
                'Contents\Model\HistoricTables' =>  function($sm) {
                    error_log("create Historic table");
                    $tableGateway = $sm->get('HistoricTableGateway');
                    $table = new HistoricTables($tableGateway);
                    return $table;
                },
                'RuleTableGateway' => function ($sm) {
                    $dbAdapter = $sm->get('Zend\Db\Adapter\Adapter');
                    $resultSetPrototype = new ResultSet();
                    $resultSetPrototype->setArrayObjectPrototype(new Rule());
                    return new TableGateway(array('r' => 'rules'), $dbAdapter, null, $resultSetPrototype);
                }, 
                'Contents\Model\OrthogrTables' =>  function($sm) {
                    error_log("create Orthogr table");
                    $tableGateway = $sm->get('OrthogrTableGateway');
                    $table = new OrthogrTables($tableGateway);
                    return $table;
                },
                'OrthogrTableGateway' => function ($sm) {
                    $dbAdapter = $sm->get('Zend\Db\Adapter\Adapter');
                    $resultSetPrototype = new ResultSet();
                    $resultSetPrototype->setArrayObjectPrototype(new Orthogr());
                    return new TableGateway(array('o' => 'orthos'), $dbAdapter, null, $resultSetPrototype);
                }, 
                'Contents\Model\FormulaTables' =>  function($sm) {
                    error_log("create Formula table");
                    $tableGateway = $sm->get('FormulaTableGateway');
                    $table = new FormulaTables($tableGateway);
                    return $table;
                },
                'FormulaTableGateway' => function ($sm) {
                    $dbAdapter = $sm->get('Zend\Db\Adapter\Adapter');
                    $resultSetPrototype = new ResultSet();
                    $resultSetPrototype->setArrayObjectPrototype(new Formula());
                    return new TableGateway(array('f' => 'formulas'), $dbAdapter, null, $resultSetPrototype);
                }, 
                'Contents\Model\ArticleTables' =>  function($sm) {
                    error_log("create Article table");
                    $tableGateway = $sm->get('ArticleTableGateway');
                    $table = new ArticleTables($tableGateway);
                    return $table;
                },
                'ArticleTableGateway' => function ($sm) {
                    $dbAdapter = $sm->get('Zend\Db\Adapter\Adapter');
                    $resultSetPrototype = new ResultSet();
                    $resultSetPrototype->setArrayObjectPrototype(new Article());
                    return new TableGateway(array('a' => 'articles'), $dbAdapter, null, $resultSetPrototype);
                }, 
                'Contents\Model\ArticlesFormulasTables' =>  function($sm) {
                    error_log("create ArticlesFormulas table");
                    $tableGateway = $sm->get('ArticlesFormulasTables');
                    $table = new ArticlesFormulasTables($tableGateway);
                    return $table;
                },
                'ArticlesFormulasTables' => function ($sm) {
                    $dbAdapter = $sm->get('Zend\Db\Adapter\Adapter');
                    $resultSetPrototype = new ResultSet();
                    $resultSetPrototype->setArrayObjectPrototype(new ArticleFormula());
                    return new TableGateway(array('af' => 'articles_formulas'), $dbAdapter, null, $resultSetPrototype);
                }, 
                'Contents\Model\ArticleAddInfoTable' =>  function($sm) {
                    error_log("create ArticleAddInfoTable");
                    $tableGateway = $sm->get('ArticleAddInfoTable');
                    $table = new ArticleAddInfoTable($tableGateway);
                    return $table;
                },
                'ArticleAddInfoTable' => function ($sm) {
                    $dbAdapter = $sm->get('Zend\Db\Adapter\Adapter');
                    $resultSetPrototype = new ResultSet();
                    $resultSetPrototype->setArrayObjectPrototype(new ArticleAddInfo());
                    return new TableGateway(array('ai' => 'articles_addinfo'), $dbAdapter, null, $resultSetPrototype);
                }, 
                'Contents\Model\WordTables' =>  function($sm) {
                    error_log("create Word table");
                    $tableGateway = $sm->get('WordTableGateway');
                    $table = new WordTables($tableGateway);
                    return $table;
                },
                'WordTableGateway' => function ($sm) {
                    $dbAdapter = $sm->get('Zend\Db\Adapter\Adapter');
                    $resultSetPrototype = new ResultSet();
                    $resultSetPrototype->setArrayObjectPrototype(new Word());
                    return new TableGateway(array('w' => 'words'), $dbAdapter, null, $resultSetPrototype);
                }, 
                'Contents\Model\AccentTables' =>  function($sm) {
                    error_log("create Accent table");
                    $tableGateway = $sm->get('AccentTableGateway');
                    $table = new WordTables($tableGateway);
                    return $table;
                },
                'AccentTableGateway' => function ($sm) {
                    $dbAdapter = $sm->get('Zend\Db\Adapter\Adapter');
                    $resultSetPrototype = new ResultSet();
                    $resultSetPrototype->setArrayObjectPrototype(new Accent());
                    return new TableGateway(array('w' => 'accents'), $dbAdapter, null, $resultSetPrototype);
                }, 
                'Contents\Model\MistakeTables' =>  function($sm) {
                    error_log("create Mistake table");
                    $tableGateway = $sm->get('MistakeTableGateway');
                    $table = new MistakeTables($tableGateway);
                    return $table;
                },
                'MistakeTableGateway' => function ($sm) {
                    $dbAdapter = $sm->get('Zend\Db\Adapter\Adapter');
                    $resultSetPrototype = new ResultSet();
                    $resultSetPrototype->setArrayObjectPrototype(new Mistake());
                    return new TableGateway(array('m' => 'mistakes'), $dbAdapter, null, $resultSetPrototype);
                }, 
                'Contents\Model\BigrammTables' =>  function($sm) {
                    error_log("create Bigramm table");
                    $tableGateway = $sm->get('BigrammTableGateway');
                    $table = new BigrammTables($tableGateway);
                    return $table;
                },
                'BigrammTableGateway' => function ($sm) {
                    $dbAdapter = $sm->get('Zend\Db\Adapter\Adapter');
                    $resultSetPrototype = new ResultSet();
                    $resultSetPrototype->setArrayObjectPrototype(new Bigramm());
                    return new TableGateway(array('b' => 'bigramms'), $dbAdapter, null, $resultSetPrototype);
                },                 
                'Contents\Model\TrigrammTables' =>  function($sm) {
                    error_log("create Trigramm table");
                    $tableGateway = $sm->get('TrigrammTableGateway');
                    $table = new TrigrammTables($tableGateway);
                    return $table;
                },
                'TrigrammTableGateway' => function ($sm) {
                    $dbAdapter = $sm->get('Zend\Db\Adapter\Adapter');
                    $resultSetPrototype = new ResultSet();
                    $resultSetPrototype->setArrayObjectPrototype(new Trigramm());
                    return new TableGateway(array('tri' => 'trigramms'), $dbAdapter, null, $resultSetPrototype);
                },                 
                'Contents\Model\TetragrammTables' =>  function($sm) {
                    error_log("create Tetragramm table");
                    $tableGateway = $sm->get('TetragrammTableGateway');
                    $table = new TetragrammTables($tableGateway);
                    return $table;
                },
                'TetragrammTableGateway' => function ($sm) {
                    $dbAdapter = $sm->get('Zend\Db\Adapter\Adapter');
                    $resultSetPrototype = new ResultSet();
                    $resultSetPrototype->setArrayObjectPrototype(new Tetragramm());
                    return new TableGateway(array('t' => 'tetragramms'), $dbAdapter, null, $resultSetPrototype);
                },  
                'Contents\Model\PredislTable' =>  function($sm) {
                    $tableGateway = $sm->get('PredislTableGateway');
                    $table = new PredislTable($tableGateway);
                    return $table;
                },
                'PredislTableGateway' => function ($sm) {
                    $dbAdapter = $sm->get('Zend\Db\Adapter\Adapter');
                    $resultSetPrototype = new ResultSet();
                    $resultSetPrototype->setArrayObjectPrototype(new Predisl());
                    return new TableGateway(['p' => 'predisl'], $dbAdapter, null, $resultSetPrototype);
                },   
                //dic_users
                'Contents\Model\DicUserTable' =>  function($sm) {
                    $tableGateway = $sm->get('DicUserTableGateway');
                    $table = new DicUserTable($tableGateway);
                    return $table;
                },
                'DicUserTableGateway' => function ($sm) {
                    $dbAdapter = $sm->get('Zend\Db\Adapter\Adapter');
                    $resultSetPrototype = new ResultSet();
                    $resultSetPrototype->setArrayObjectPrototype(new DicUser());
                    return new TableGateway(['d' => 'dic_users'], $dbAdapter, null, $resultSetPrototype);
                },   
                //status
                'Contents\Model\StateTable' =>  function($sm) {
                    $tableGateway = $sm->get('StateTableGateway');
                    $table = new StateTable($tableGateway);
                    return $table;
                },
                'StateTableGateway' => function ($sm) {
                    $dbAdapter = $sm->get('Zend\Db\Adapter\Adapter');
                    $resultSetPrototype = new ResultSet();
                    $resultSetPrototype->setArrayObjectPrototype(new State());
                    return new TableGateway(['s' => 'status'], $dbAdapter, null, $resultSetPrototype);
                },
                // external sources   
                'Contents\Model\SourcesTable' =>  function($sm) {
                    $tableGateway = $sm->get('SourcesTableGateway');
                    $table = new SourcesTable($tableGateway);
                    return $table;
                },
                'SourcesTableGateway' => function ($sm) {
                    $dbAdapter = $sm->get('Zend\Db\Adapter\Adapter');
                    $resultSetPrototype = new ResultSet();
                    $resultSetPrototype->setArrayObjectPrototype(new Sources());
                    return new TableGateway(['s' => 'sources'], $dbAdapter, null, $resultSetPrototype);
                },
            ),
        );
    }    
    
}
