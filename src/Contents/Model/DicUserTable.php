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
 use Zend\Db\Sql\Delete;
 use Zend\Db\Sql\Sql;
 use Zend\Db\Sql\Expression;
 use Zend\Crypt\Password\Bcrypt;
 
 class DicUserTable
 {
    protected $tableGateway;
    const Incorrect = 1;
    const Added = 2;
    const None = 3;
    const Exists = 4;
    const Deleted = 5;

    public function __construct(TableGateway $tableGateway)
    {
        $this->tableGateway = $tableGateway;
    }

    public function fetchAll()
    {
        $resultSet = $this->tableGateway->select();
        return $resultSet;
    }
     
	public static function getFullName($sm, $username)
	{
		$table = $sm->get('Contents\Model\DicUserTable');
		$user = $table->tableGateway->select(function(Select $select) use ($username)
	    {
			$select->where('d.username LIKE \''.$username.'\'');
		});
		if (count($user) > 0)
			return $user->current()->fullname;
		return null;
	}

    public static function userExists($sm, $name)
    {
		$table = $sm->get('Contents\Model\DicUserTable');
		$user = $table->tableGateway->select(function(Select $select) use ($name)
	    {
			$select->where('d.username LIKE \''.$name.'\'');
		});
		if (count($user) > 0) {
		    return true;
		   }
		return false;
    }
    
    public static function addUser($sm, $name, $password, $fullname)
    {
		$table = $sm->get('Contents\Model\DicUserTable');
		$bcrypt = new Bcrypt();
		$securePass = $bcrypt->create($password);		
	    $table->tableGateway->insert(array('username' => $name,
	                'fullname' => $fullname,
	                'password' => $securePass
         ));
    }
    public static function deleteUser($sm, $name)
    {
		$table = $sm->get('Contents\Model\DicUserTable');
		$action = new Delete('dic_users');
        $action->where(array('username=?' => $name));

         $sql    = new Sql($sm->get('Zend\Db\Adapter\Adapter'));
         $stmt   = $sql->prepareStatementForSqlObject($action);
         $result = $stmt->execute();		
    }
	
 } 
 
 ?>
 