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

use Contents\Model\Abc;
use Contents\Model\AbcTables;

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
                // abc_a             
                'Contents\Model\AbcTables_a' =>  function($sm) {
                    $tableGateway = $sm->get('AbcTableGateway_a');
                    $table = new AbcTables($tableGateway);
                    return $table;
                },
                'AbcTableGateway_a' => function ($sm) {
                    $dbAdapter = $sm->get('Zend\Db\Adapter\Adapter');
                    $resultSetPrototype = new ResultSet();
                    $resultSetPrototype->setArrayObjectPrototype(new Abc());
                    return new TableGateway(['a' => 'abc_a'], $dbAdapter, null, $resultSetPrototype);
                },  
				// abc_be
                'Contents\Model\AbcTables_be' =>  function($sm) {
                    $tableGateway = $sm->get('AbcTableGateway_be');
                    $table = new AbcTables($tableGateway);
                    return $table;
                },
                'AbcTableGateway_be' => function ($sm) {
                    $dbAdapter = $sm->get('Zend\Db\Adapter\Adapter');
                    $resultSetPrototype = new ResultSet();
                    $resultSetPrototype->setArrayObjectPrototype(new Abc());
                    return new TableGateway(['be' => 'abc_be'], $dbAdapter, null, $resultSetPrototype);
                },  
				// abc_che
                'Contents\Model\AbcTables_che' =>  function($sm) {
                    $tableGateway = $sm->get('AbcTableGateway_che');
                    $table = new AbcTables($tableGateway);
                    return $table;
                },
                'AbcTableGateway_che' => function ($sm) {
                    $dbAdapter = $sm->get('Zend\Db\Adapter\Adapter');
                    $resultSetPrototype = new ResultSet();
                    $resultSetPrototype->setArrayObjectPrototype(new Abc());
                    return new TableGateway(['che' => 'abc_che'], $dbAdapter, null, $resultSetPrototype);
                },  
				// abc_de
                'Contents\Model\AbcTables_de' =>  function($sm) {
                    $tableGateway = $sm->get('AbcTableGateway_de');
                    $table = new AbcTables($tableGateway);
                    return $table;
                },
                'AbcTableGateway_de' => function ($sm) {
                    $dbAdapter = $sm->get('Zend\Db\Adapter\Adapter');
                    $resultSetPrototype = new ResultSet();
                    $resultSetPrototype->setArrayObjectPrototype(new Abc());
                    return new TableGateway(['de' => 'abc_de'], $dbAdapter, null, $resultSetPrototype);
                },  
				// abc_e
                'Contents\Model\AbcTables_e' =>  function($sm) {
                    $tableGateway = $sm->get('AbcTableGateway_e');
                    $table = new AbcTables($tableGateway);
                    return $table;
                },
                'AbcTableGateway_e' => function ($sm) {
                    $dbAdapter = $sm->get('Zend\Db\Adapter\Adapter');
                    $resultSetPrototype = new ResultSet();
                    $resultSetPrototype->setArrayObjectPrototype(new Abc());
                    return new TableGateway(['e' => 'abc_e'], $dbAdapter, null, $resultSetPrototype);
                },  
				// abc_ef
                'Contents\Model\AbcTables_ef' =>  function($sm) {
                    $tableGateway = $sm->get('AbcTableGateway_ef');
                    $table = new AbcTables($tableGateway);
                    return $table;
                },
                'AbcTableGateway_ef' => function ($sm) {
                    $dbAdapter = $sm->get('Zend\Db\Adapter\Adapter');
                    $resultSetPrototype = new ResultSet();
                    $resultSetPrototype->setArrayObjectPrototype(new Abc());
                    return new TableGateway(['ef' => 'abc_ef'], $dbAdapter, null, $resultSetPrototype);
                },  
				// abc_el
                'Contents\Model\AbcTables_el' =>  function($sm) {
                    $tableGateway = $sm->get('AbcTableGateway_el');
                    $table = new AbcTables($tableGateway);
                    return $table;
                },
                'AbcTableGateway_el' => function ($sm) {
                    $dbAdapter = $sm->get('Zend\Db\Adapter\Adapter');
                    $resultSetPrototype = new ResultSet();
                    $resultSetPrototype->setArrayObjectPrototype(new Abc());
                    return new TableGateway(['el' => 'abc_el'], $dbAdapter, null, $resultSetPrototype);
                },  
				// abc_em
                'Contents\Model\AbcTables_em' =>  function($sm) {
                    $tableGateway = $sm->get('AbcTableGateway_em');
                    $table = new AbcTables($tableGateway);
                    return $table;
                },
                'AbcTableGateway_em' => function ($sm) {
                    $dbAdapter = $sm->get('Zend\Db\Adapter\Adapter');
                    $resultSetPrototype = new ResultSet();
                    $resultSetPrototype->setArrayObjectPrototype(new Abc());
                    return new TableGateway(['em' => 'abc_em'], $dbAdapter, null, $resultSetPrototype);
                },  
				// abc_en
                'Contents\Model\AbcTables_en' =>  function($sm) {
                    $tableGateway = $sm->get('AbcTableGateway_en');
                    $table = new AbcTables($tableGateway);
                    return $table;
                },
                'AbcTableGateway_en' => function ($sm) {
                    $dbAdapter = $sm->get('Zend\Db\Adapter\Adapter');
                    $resultSetPrototype = new ResultSet();
                    $resultSetPrototype->setArrayObjectPrototype(new Abc());
                    return new TableGateway(['en' => 'abc_en'], $dbAdapter, null, $resultSetPrototype);
                },  
				// abc_er
                'Contents\Model\AbcTables_er' =>  function($sm) {
                    $tableGateway = $sm->get('AbcTableGateway_er');
                    $table = new AbcTables($tableGateway);
                    return $table;
                },
                'AbcTableGateway_er' => function ($sm) {
                    $dbAdapter = $sm->get('Zend\Db\Adapter\Adapter');
                    $resultSetPrototype = new ResultSet();
                    $resultSetPrototype->setArrayObjectPrototype(new Abc());
                    return new TableGateway(['er' => 'abc_er'], $dbAdapter, null, $resultSetPrototype);
                },  
				// abc_ery
                'Contents\Model\AbcTables_ery' =>  function($sm) {
                    $tableGateway = $sm->get('AbcTableGateway_ery');
                    $table = new AbcTables($tableGateway);
                    return $table;
                },
                'AbcTableGateway_ery' => function ($sm) {
                    $dbAdapter = $sm->get('Zend\Db\Adapter\Adapter');
                    $resultSetPrototype = new ResultSet();
                    $resultSetPrototype->setArrayObjectPrototype(new Abc());
                    return new TableGateway(['ery' => 'abc_ery'], $dbAdapter, null, $resultSetPrototype);
                },  
				// abc_es
                'Contents\Model\AbcTables_es' =>  function($sm) {
                    $tableGateway = $sm->get('AbcTableGateway_es');
                    $table = new AbcTables($tableGateway);
                    return $table;
                },
                'AbcTableGateway_es' => function ($sm) {
                    $dbAdapter = $sm->get('Zend\Db\Adapter\Adapter');
                    $resultSetPrototype = new ResultSet();
                    $resultSetPrototype->setArrayObjectPrototype(new Abc());
                    return new TableGateway(['es' => 'abc_es'], $dbAdapter, null, $resultSetPrototype);
                },  
				// abc_ghe
                'Contents\Model\AbcTables_ghe' =>  function($sm) {
                    $tableGateway = $sm->get('AbcTableGateway_ghe');
                    $table = new AbcTables($tableGateway);
                    return $table;
                },
                'AbcTableGateway_ghe' => function ($sm) {
                    $dbAdapter = $sm->get('Zend\Db\Adapter\Adapter');
                    $resultSetPrototype = new ResultSet();
                    $resultSetPrototype->setArrayObjectPrototype(new Abc());
                    return new TableGateway(['ghe' => 'abc_ghe'], $dbAdapter, null, $resultSetPrototype);
                },  
				// abc_ha
                'Contents\Model\AbcTables_ha' =>  function($sm) {
                    $tableGateway = $sm->get('AbcTableGateway_ha');
                    $table = new AbcTables($tableGateway);
                    return $table;
                },
                'AbcTableGateway_ha' => function ($sm) {
                    $dbAdapter = $sm->get('Zend\Db\Adapter\Adapter');
                    $resultSetPrototype = new ResultSet();
                    $resultSetPrototype->setArrayObjectPrototype(new Abc());
                    return new TableGateway(['ha' => 'abc_ha'], $dbAdapter, null, $resultSetPrototype);
                },  
				// abc_i
                'Contents\Model\AbcTables_i' =>  function($sm) {
                    $tableGateway = $sm->get('AbcTableGateway_i');
                    $table = new AbcTables($tableGateway);
                    return $table;
                },
                'AbcTableGateway_i' => function ($sm) {
                    $dbAdapter = $sm->get('Zend\Db\Adapter\Adapter');
                    $resultSetPrototype = new ResultSet();
                    $resultSetPrototype->setArrayObjectPrototype(new Abc());
                    return new TableGateway(['i' => 'abc_i'], $dbAdapter, null, $resultSetPrototype);
                },  
				// abc_ka
                'Contents\Model\AbcTables_ka' =>  function($sm) {
                    $tableGateway = $sm->get('AbcTableGateway_ka');
                    $table = new AbcTables($tableGateway);
                    return $table;
                },
                'AbcTableGateway_ka' => function ($sm) {
                    $dbAdapter = $sm->get('Zend\Db\Adapter\Adapter');
                    $resultSetPrototype = new ResultSet();
                    $resultSetPrototype->setArrayObjectPrototype(new Abc());
                    return new TableGateway(['ka' => 'abc_ka'], $dbAdapter, null, $resultSetPrototype);
                },  
				// abc_o
                'Contents\Model\AbcTables_o' =>  function($sm) {
                    $tableGateway = $sm->get('AbcTableGateway_o');
                    $table = new AbcTables($tableGateway);
                    return $table;
                },
                'AbcTableGateway_o' => function ($sm) {
                    $dbAdapter = $sm->get('Zend\Db\Adapter\Adapter');
                    $resultSetPrototype = new ResultSet();
                    $resultSetPrototype->setArrayObjectPrototype(new Abc());
                    return new TableGateway(['o' => 'abc_o'], $dbAdapter, null, $resultSetPrototype);
                },  
				// abc_pe
                'Contents\Model\AbcTables_pe' =>  function($sm) {
                    $tableGateway = $sm->get('AbcTableGateway_pe');
                    $table = new AbcTables($tableGateway);
                    return $table;
                },
                'AbcTableGateway_pe' => function ($sm) {
                    $dbAdapter = $sm->get('Zend\Db\Adapter\Adapter');
                    $resultSetPrototype = new ResultSet();
                    $resultSetPrototype->setArrayObjectPrototype(new Abc());
                    return new TableGateway(['pe' => 'abc_pe'], $dbAdapter, null, $resultSetPrototype);
                },  
				// abc_reverse_e
                'Contents\Model\AbcTables_reverse_e' =>  function($sm) {
                    $tableGateway = $sm->get('AbcTableGateway_reverse_e');
                    $table = new AbcTables($tableGateway);
                    return $table;
                },
                'AbcTableGateway_reverse_e' => function ($sm) {
                    $dbAdapter = $sm->get('Zend\Db\Adapter\Adapter');
                    $resultSetPrototype = new ResultSet();
                    $resultSetPrototype->setArrayObjectPrototype(new Abc());
                    return new TableGateway(['reverse_e' => 'abc_reverse_e'], $dbAdapter, null, $resultSetPrototype);
                },  
				// abc_sha
                'Contents\Model\AbcTables_sha' =>  function($sm) {
                    $tableGateway = $sm->get('AbcTableGateway_sha');
                    $table = new AbcTables($tableGateway);
                    return $table;
                },
                'AbcTableGateway_sha' => function ($sm) {
                    $dbAdapter = $sm->get('Zend\Db\Adapter\Adapter');
                    $resultSetPrototype = new ResultSet();
                    $resultSetPrototype->setArrayObjectPrototype(new Abc());
                    return new TableGateway(['sha' => 'abc_sha'], $dbAdapter, null, $resultSetPrototype);
                },  
				// abc_shcha
                'Contents\Model\AbcTables_shcha' =>  function($sm) {
                    $tableGateway = $sm->get('AbcTableGateway_shcha');
                    $table = new AbcTables($tableGateway);
                    return $table;
                },
                'AbcTableGateway_shcha' => function ($sm) {
                    $dbAdapter = $sm->get('Zend\Db\Adapter\Adapter');
                    $resultSetPrototype = new ResultSet();
                    $resultSetPrototype->setArrayObjectPrototype(new Abc());
                    return new TableGateway(['shcha' => 'abc_shcha'], $dbAdapter, null, $resultSetPrototype);
                },  
				// abc_short_i
                'Contents\Model\AbcTables_short_i' =>  function($sm) {
                    $tableGateway = $sm->get('AbcTableGateway_short_i');
                    $table = new AbcTables($tableGateway);
                    return $table;
                },
                'AbcTableGateway_short_i' => function ($sm) {
                    $dbAdapter = $sm->get('Zend\Db\Adapter\Adapter');
                    $resultSetPrototype = new ResultSet();
                    $resultSetPrototype->setArrayObjectPrototype(new Abc());
                    return new TableGateway(['short_i' => 'abc_short_i'], $dbAdapter, null, $resultSetPrototype);
                },  
				// abc_te
                'Contents\Model\AbcTables_te' =>  function($sm) {
                    $tableGateway = $sm->get('AbcTableGateway_te');
                    $table = new AbcTables($tableGateway);
                    return $table;
                },
                'AbcTableGateway_te' => function ($sm) {
                    $dbAdapter = $sm->get('Zend\Db\Adapter\Adapter');
                    $resultSetPrototype = new ResultSet();
                    $resultSetPrototype->setArrayObjectPrototype(new Abc());
                    return new TableGateway(['te' => 'abc_te'], $dbAdapter, null, $resultSetPrototype);
                },  
				// abc_tse
                'Contents\Model\AbcTables_tse' =>  function($sm) {
                    $tableGateway = $sm->get('AbcTableGateway_tse');
                    $table = new AbcTables($tableGateway);
                    return $table;
                },
                'AbcTableGateway_tse' => function ($sm) {
                    $dbAdapter = $sm->get('Zend\Db\Adapter\Adapter');
                    $resultSetPrototype = new ResultSet();
                    $resultSetPrototype->setArrayObjectPrototype(new Abc());
                    return new TableGateway(['tse' => 'abc_tse'], $dbAdapter, null, $resultSetPrototype);
                },  
				// abc_u
                'Contents\Model\AbcTables_u' =>  function($sm) {
                    $tableGateway = $sm->get('AbcTableGateway_u');
                    $table = new AbcTables($tableGateway);
                    return $table;
                },
                'AbcTableGateway_u' => function ($sm) {
                    $dbAdapter = $sm->get('Zend\Db\Adapter\Adapter');
                    $resultSetPrototype = new ResultSet();
                    $resultSetPrototype->setArrayObjectPrototype(new Abc());
                    return new TableGateway(['u' => 'abc_u'], $dbAdapter, null, $resultSetPrototype);
                },  
				// abc_ve
                'Contents\Model\AbcTables_ve' =>  function($sm) {
                    $tableGateway = $sm->get('AbcTableGateway_ve');
                    $table = new AbcTables($tableGateway);
                    return $table;
                },
                'AbcTableGateway_ve' => function ($sm) {
                    $dbAdapter = $sm->get('Zend\Db\Adapter\Adapter');
                    $resultSetPrototype = new ResultSet();
                    $resultSetPrototype->setArrayObjectPrototype(new Abc());
                    return new TableGateway(['ve' => 'abc_ve'], $dbAdapter, null, $resultSetPrototype);
                },  
				// abc_ya
                'Contents\Model\AbcTables_ya' =>  function($sm) {
                    $tableGateway = $sm->get('AbcTableGateway_ya');
                    $table = new AbcTables($tableGateway);
                    return $table;
                },
                'AbcTableGateway_ya' => function ($sm) {
                    $dbAdapter = $sm->get('Zend\Db\Adapter\Adapter');
                    $resultSetPrototype = new ResultSet();
                    $resultSetPrototype->setArrayObjectPrototype(new Abc());
                    return new TableGateway(['ya' => 'abc_ya'], $dbAdapter, null, $resultSetPrototype);
                },  
				// abc_yu
                'Contents\Model\AbcTables_yu' =>  function($sm) {
                    $tableGateway = $sm->get('AbcTableGateway_yu');
                    $table = new AbcTables($tableGateway);
                    return $table;
                },
                'AbcTableGateway_yu' => function ($sm) {
                    $dbAdapter = $sm->get('Zend\Db\Adapter\Adapter');
                    $resultSetPrototype = new ResultSet();
                    $resultSetPrototype->setArrayObjectPrototype(new Abc());
                    return new TableGateway(['yu' => 'abc_yu'], $dbAdapter, null, $resultSetPrototype);
                },  
				// abc_ze
                'Contents\Model\AbcTables_ze' =>  function($sm) {
                    $tableGateway = $sm->get('AbcTableGateway_ze');
                    $table = new AbcTables($tableGateway);
                    return $table;
                },
                'AbcTableGateway_ze' => function ($sm) {
                    $dbAdapter = $sm->get('Zend\Db\Adapter\Adapter');
                    $resultSetPrototype = new ResultSet();
                    $resultSetPrototype->setArrayObjectPrototype(new Abc());
                    return new TableGateway(['ze' => 'abc_ze'], $dbAdapter, null, $resultSetPrototype);
                },  
				// abc_zhe
                'Contents\Model\AbcTables_zhe' =>  function($sm) {
                    $tableGateway = $sm->get('AbcTableGateway_zhe');
                    $table = new AbcTables($tableGateway);
                    return $table;
                },
                'AbcTableGateway_zhe' => function ($sm) {
                    $dbAdapter = $sm->get('Zend\Db\Adapter\Adapter');
                    $resultSetPrototype = new ResultSet();
                    $resultSetPrototype->setArrayObjectPrototype(new Abc());
                    return new TableGateway(['zhe' => 'abc_zhe'], $dbAdapter, null, $resultSetPrototype);
                },  
            ),
        );
    }    
}
