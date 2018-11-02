<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/ZendSkeletonApplication for the canonical source repository
 * @copyright Copyright (c) 2005-2015 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */

namespace Contents\Model;

 use Zend\Db\TableGateway\TableGateway;
 use Zend\Db\Sql\Select;
 use Zend\Db\Sql\Sql;
    
 class ContentsTables
 {
     protected $tableGateway;

     public function __construct(TableGateway $tableGateway)
     {
         $this->tableGateway = $tableGateway;
     }

     public static function getContents($sm)
     {
        $table = $sm->get('Contents\Model\ContentsTables');
        
        $partsSet = $table->tableGateway->select();
        $partsSet->buffer();
        $contents = array();  
		//error_log(sprintf("this->url: %s", $sm->get('Request')->getRequestUri()));
		$url = $sm->get('Request')->getRequestUri();
		$para_page = 0;
		if (strpos($url, "/paragraph") !== false) {
			$para_page = substr($url, strpos($url, "/paragraph/") + strlen("/paragraph/"));
			if ($para_page == "")
				$para_page = "1";
			$para_page = intval($para_page);
			error_log(sprintf("cur para = %d", $para_page));
		}
        foreach ($partsSet as $part)
        {
        	//error_log("part1 = ");
            $contents[$part->name] = array('id' => $part->id, 'tiles' => array());
            $tilesSet = $table->tableGateway->select(function(Select $select) use ($part) 
            {
                $select->join(array('pt' => 'parts_tiles'), 'pt.id_part = p.id', array('id_tile', 'id_part'), 'left');
                $select->join(array('t' => 'tiles'), 'pt.id_tile = t.id', array('id', 'name' => 'title'), 'left');
                $select->where('p.id = '.strval($part->id));
            });
    
        	//error_log("part2 = ");
            
            $adapter = $table->tableGateway->adapter;
            $sql = new Sql($adapter);
            foreach ($tilesSet as $tile)
            {
	        	//error_log("tile1 = ");
                $select = $sql->select();
                $select->from(array('t' => 'tiles'));
                $select->where(array('t.id' => $tile->id));
                $select->join(array('tp' => 'tiles_paras'), 'tp.id_tile = t.id', array('id_tile', 'id_para'), 'left');
                $select->join(array('para' => 'paras'), 'tp.id_para = para.id', array('id', 'name', 'examples'), 'left');

                $selectString = $sql->getSqlStringForSqlObject($select);
                $paraSet = $adapter->query($selectString, $adapter::QUERY_MODE_EXECUTE);                

                $contents[$part->name]['tiles'][$tile->name] = array('id' => $tile->id, 'active' => "0", 'paras' => array());
                $pp = "§ ";
	        	//error_log("tile2 = ");
                foreach ($paraSet as $para)
                {
//		        	error_log("para1 = ");
					if ($para->id == $para_page)
						$contents[$part->name]['tiles'][$tile->name]['active'] = "1";
                    $contents[$part->name]['tiles'][$tile->name]['paras'][] = array('id' => $para->id,
                                                                                    'name' => $para->name, 
                                                                                    'examples' => $para->examples);    
                };
                $pp .= strval($contents[$part->name]['tiles'][$tile->name]['paras'][0]['id'])."-";
                $pp .= strval($contents[$part->name]['tiles'][$tile->name]['paras'][count($contents[$part->name]['tiles'][$tile->name]['paras']) - 1]['id']);
                $contents[$part->name]['tiles'][$tile->name]['pp'] = $pp;
            }
         }
        // print_r($contents);
         return $contents;         
     }
 } 
 
 ?>
 