<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/ZendSkeletonApplication for the canonical source repository
 * @copyright Copyright (c) 2005-2015 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */

namespace Admin\Model;

 use Contents\Model\ArticleTables;
 use Contents\Model\DicUserTable;

 use Zend\Db\TableGateway\TableGateway;
 use Zend\Db\Sql\Select;
 use Zend\Db\Sql\Delete;
 use Zend\Db\Sql\Sql;
 use Zend\Db\Sql\Expression;
 
 class ChangesTable
 {
    protected $tableGateway;
    
    const Made = 2;
    const Expects = 1;
    
    const Phantom = 1;
    const DeleteWait = 2;
    const Delete = 3;
    const NewArt = 4;
    const Edited = 5;
    
	const FOR_PAGE_COUNT = 100;
    
    public function __construct(TableGateway $tableGateway)
    {
        $this->tableGateway = $tableGateway;
    }

    public function fetchAll()
    {
        $resultSet = $this->tableGateway->select();
        return $resultSet;
    }
    
    public static function getAll($sm)
    {
		$table = $sm->get('Admin\Model\ChangesTable');
		$changes = $table->tableGateway->select(function(Select $select)
	    {
	    	$select->order('c.status, c.id');
		});
		if (count($changes) > 0) {
			$res = array();
			foreach ($changes as $item) {
				$res[] = array('id' => $item->id,
				                       'id_article' => $item->id_article,
				                       'text' => $item->text,
				                       'src' => $item->src,
				                       'action' => $item->action,
				                       'status' => $item->status,
				                       'chd' => $item->chd,
				                       'username' => $item->username);
			}
			return $res;
		}
		return null;
    }

    public static function getForPage($sm, $page)
    {
		$table = $sm->get('Admin\Model\ChangesTable');
		$changes = $table->tableGateway->select(function(Select $select)
	    {
	    	$select->columns(array('id'));
	    	$select->order(new \Zend\Db\Sql\Expression('UNIX_TIMESTAMP(c.chd) DESC, c.status, c.id'));
		});
//		print_r($changes);
		if (count($changes) > 0 ) {
			if (count($changes) > ChangesTable::FOR_PAGE_COUNT) {
				$res_id = array();
				foreach ($changes as $ch)
					$res_id[] = $ch->id;
				$paginator = new \Zend\Paginator\Paginator(new \Zend\Paginator\Adapter\ArrayAdapter($res_id)); 
				$paginator->setItemCountPerPage(ChangesTable::FOR_PAGE_COUNT);
				$paginator->setCurrentPageNumber((int)$page);
				$res_id = array();
				foreach ($paginator->getCurrentItems() as $item) {
					$res_id[] = $item;
				}
	        	$comma_separated = implode(",", $res_id);
	        	//error_log($comma_separated);
				$changes = $table->tableGateway->select(function(Select $select) use ($comma_separated)
	    		{
	    			$select->where('c.id IN ('.$comma_separated.')');
	   	    	   $select->order(new \Zend\Db\Sql\Expression('UNIX_TIMESTAMP(c.chd) DESC, c.status, c.id'));
				});
			}
			else {
				$changes = $table->tableGateway->select(function(Select $select)
	    		{
	    			//$select->columns(array('id'));
	   	    	   $select->order(new \Zend\Db\Sql\Expression('UNIX_TIMESTAMP(c.chd) DESC, c.status, c.id'));
				});
			}			
			$res = array('paginator' => $paginator, 'show' => array());//, 'count' => count($res_id));
			foreach ($changes as $item) {
				$res['show'][] = array('id' => $item->id,
				                       			  'id_article' => $item->id_article,
				                       			  'text' => $item->text,
				                       			  'src' => $item->src,
				                       			  'action' => $item->action,
				                       			  'status' => $item->status,
				                       			  'chd' => ChangesTable::getDate($item->chd),
				                       			  'username' => $item->username);
			} 
			return $res;
		}
		return null;
    }

    public static function getForProcessing($sm)
    {
		$table = $sm->get('Admin\Model\ChangesTable');
		$changes = $table->tableGateway->select(function(Select $select)
	    {
	    	$select->where('(c.status=2 AND c.action='.strval(ChangesTable::DeleteWait).') OR (c.status=1 AND  (c.action='.strval(ChangesTable::Edited).' OR c.action='.strval(ChangesTable::NewArt).'))');
		});
		//print_r($changes);
		if (count($changes) > 0) {
			$res = array();
			foreach ($changes as $item) {
				$res[] = array('id' => $item->id,
				                       'id_article' => $item->id_article,
				                       'text' => $item->text,
				                       'action' => $item->action,
				                       'dic' => $item->dic);
			}
			return $res;
		}
		return null;
    }
	
	public static function cancelChange($sm, $id)
	{
        // delete
		$action = new Delete('changes');
        $action->where(array('id = ?' => $id));

         $sql    = new Sql($sm->get('Zend\Db\Adapter\Adapter'));
         $stmt   = $sql->prepareStatementForSqlObject($action);
         $result = $stmt->execute();		
	}
	
    public static function addToPhantoms($sm, $authService, $id)
    {
		$table = $sm->get('Admin\Model\ChangesTable');
/*		$change = $table->tableGateway->select(function(Select $select) use ($id)
		{
	    	$select->where('c.id='.strval($id));
		});
	    if (count($change) == 0) { */
	        $src = ArticleTables::getSrc($sm, $id);
	        $text = ArticleTables::getText($sm, $id); 
	        $title = ArticleTables::getTitle($sm, $id); 
	       // $author = 
	        $table->tableGateway->insert(array('id_article' => $id,
	                'title' => $title,
                    'text' => $text,
                    'src' => $src,
                    'action' => self::Phantom,
                    'status' => self::Expects,
                    'chd' => new Expression('NOW()'),
                    'username' => DicUserTable::getFullName($sm, $authService->getIdentity()),
            ));
	   // }
	}
    public static function addToDelete($sm, $authService, $id)
    {
		$table = $sm->get('Admin\Model\ChangesTable');
/*		$change = $table->tableGateway->select(function(Select $select) use ($id)
		{
	    	$select->where('c.id='.strval($id));
		});
	    if (count($change) == 0) { */
	        $text = ArticleTables::getText($sm, $id); 
	        $title = ArticleTables::getTitle($sm, $id); 
	        $table->tableGateway->insert(array('id_article' => $id,
	                'title' => $title,
                    'text' => $text,
                    'src' => $text,
                    'action' => self::DeleteWait,
                    'status' => self::Made, //self::Expects,
                    'chd' => new Expression('NOW()'),
                    'username' => DicUserTable::getFullName($sm, $authService->getIdentity()),
            ));
	    //}
	}
	public static function addEdited($sm, $authService, $id, $text)
	{
		$table = $sm->get('Admin\Model\ChangesTable');
	    $src = ArticleTables::getText($sm, $id); 
	    $title = ArticleTables::getTitle($sm, $id); 
	    $table->tableGateway->insert(array('id_article' => $id,
	                'title' => $title,
                    'text' => $text,
                    'src' => $src,
                    'action' => self::Edited,
                    'status' => self::Expects,
                    'chd' => new Expression('NOW()'),
                    'username' => DicUserTable::getFullName($sm, $authService->getIdentity()),
         ));
	}
	public static function addNew($sm, $authService, $text, $dic)
	{
		if ($dic == 0)
			$dic = 50;
		else
			$dic = 49;
		$table = $sm->get('Admin\Model\ChangesTable');
	    $table->tableGateway->insert(array('id_article' => 0xFF,
	                'title' => '',
                    'text' => $text,
                    'src' => '',
                    'dic' => $dic,
                    'action' => self::NewArt,
                    'status' => self::Expects,
                    'chd' => new Expression('NOW()'),
                    'username' => DicUserTable::getFullName($sm, $authService->getIdentity()),
         ));
	}

    public static function getDate($last)
    {
            $ar = strptime ($last, '%Y-%m-%d');
             return sprintf('%02d-%02d-%d', $ar['tm_mday'], $ar['tm_mon'] + 1, $ar['tm_year'] + 1900);            
    }

    public static function processDiacritics($str)
    {
//    	return $str;
   		$dmap = array(
				"&Aacute;"	=> "\xc3\x81", //Á
				"&aacute;"	=> "\xc3\xa1", //á
				"&Agrave;"	=> "\xc3\x80", //À
				"&agrave;"	=> "\xc3\xa0", //à
				"&Acirc;"	=> "\xc3\x82", //Â
				"&acirc;"	=> "\xc3\xa2", //â
				"&Auml;"	=> "\xc3\x84", //Ä
				"&auml;"	=> "\xc3\xa4", //ä
				"&Atilde;"	=> "\xc3\x83", //Ã
				"&atilde;"	=> "\xc3\xa3", //ã
				"&Aring;"	=> "\xc3\x85", //Å
				"&aring;"	=> "\xc3\xa5", //å
				"&Aelig;"	=> "\xc3\x86", //Æ
				"&aelig;"	=> "\xc3\xa6", //æ
				"&Ccedil;"		=> "\xc3\x87", //Ç
				"&ccedil;"		=> "\xc3\xa7", //ç
				"&Eth;"		=> "\xc3\x90", //Ð
				"&eth;"		=> "\xc3\xb0", //ð
				"&Eacute;"		=> "\xc3\x89", //É
				"&eacute;"	=> "\xc3\xa9", //é
				"&Egrave;"	=> "\xc3\x88", //È
				"&egrave;"	=> "\xc3\xa8", //è
				"&Ecirc;"	=> "\xc3\x8a", //Ê
				"&ecirc;"	=> "\xc3\xaa", //ê
				"&Euml;"	=> "\xc3\x8b", //Ë
				"&euml;"	=> "\xc3\xab", //ë
				"&Iacute;"		=> "\xc3\x8d", //Í
				"&iacute;"		=> "\xc3\xad", //í
				"&Igrave;"		=> "\xc3\x8c", //Ì
				"&igrave;"		=> "\xc3\xac", //ì
				"&Icirc;"		=> "\xc3\x8e", //Î
				"&icirc;"	=> "\xc3\xae", //î
				"&Iuml;"	=> "\xc3\x8f", //Ï
				"&iuml;"	=> "\xc3\xaf", //ï
				"&Ntilde;"  	=> "\xc3\x91", //Ñ
				"&ntilde;"	=> "\xc3\xb1", //ñ
				"&Oacute;"	=> "\xc3\x93", //Ó
				"&oacute;"	=> "\xc3\xb3", //ó
				"&Ograve;"	=> "\xc3\x92", //Ò
				"&ograve;"	=> "\xc3\xb2", //ò
				"&Ocirc;"	=> "\xc3\x94", //Ô
				"&ocirc;"	=> "\xc3\xb4", //ô
				"&Ouml;"	=> "\xc3\x96", //Ö
				"&ouml;"	=> "\xc3\xb6", //ö
				"&Otilde;"		=> "\xc3\x95", //Õ
				"&otilde;"		=> "\xc3\xb5", //õ
				"&Oslash;"	=> "\xc3\x98", //Ø
				"&oslash;"	=> "\xc3\xb8", //ø
				"&szlig;"		=> "\xc3\x9f", //ß
				"&Thorn;"		=> "\xc3\x9e", //Þ
				"&thorn;"		=> "\xc3\xbe", //þ
				"&Uacute;"	=> "\xc3\x9a", //Ú
				"&uacute;"	=> "\xc3\xba", //ú
				"&Ugrave;"	=> "\xc3\x99", //Ù
				"&ugrave;"	=> "\xc3\xb9", //ù
				"&Ucirc;"	=> "\xc3\x9b", //Û
				"&ucirc;" 	=> "\xc3\xbb", //û
				"&Uuml;"	=> "\xc3\x9c", //Ü
				"&uuml;"	=> "\xc3\xbc",  //ü
				"&Yacute;"	=> "\xc3\x9d", //Ý
				"&yacute;"	=> "\xc3\xbd", //ý
				"&yuml;"	=> "\xc3\xbf",   //ÿ
				"&scaron;" => "\xc5\xa1", //š
/*				"&ccaron;" => "\xc4\x8d",  //č */
		);
		error_log($str);
		foreach ($dmap as $key => $value)
		{
//			error_log($key);
			$pos = strpos($str, $key);
			if ($pos !== false) {
//				error_log($key);
//				error_log($value);
				$str = str_replace($key, $value, $str); 
//				error_log($str);
			}
		}
		return $str;
    }
 } 
 
 ?>
 