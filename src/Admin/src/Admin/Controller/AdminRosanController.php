<?php
/**
 * @link      http://github.com/zendframework/ZendSkeletonApplication for the canonical source repository
 * @copyright Copyright (c) 2005-2016 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */

namespace Admin\Controller;

use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\ViewModel;
use Zend\View\Model\JsonModel;
use Interop\Container\ContainerInterface;
use Zend\ServiceManager\ServiceLocatorInterface;
use Zend\Form\View\Helper\FormSelect;
use Zend\Form\View\Helper\FormRow;

use Zend\Mvc\MvcEvent;

use Admin\Form\LoginForm;
use Admin\Form\LogoutForm;
use Admin\Form\ArticleActionsForm;
use Admin\Form\ArticleEditForm;
use Admin\Form\ArticleNewForm;
use Admin\Form\ArticleAddInfoForm;
use Admin\Form\ActionCancelForm;
use Admin\Form\GetDicForm;
use Admin\Form\RebuildForm;
use Admin\Form\AddUserForm;

use Contents\Model\DicUserTable;
use Contents\Model\ArticleTables;
use Contents\Model\ArticleAddInfoTable;
use Contents\Model\OrthogrTables;
use Contents\Model\FormulaTables;

use Admin\Model\ChangesTable;
use Contents\Model\StateTable;
use Contents\Model\SourcesTable;

use Admin\Form\AdminWordForm;
use Application\Controller\SearchController;

use Admin\Model\DicsTable;

class AdminRosanController extends AbstractActionController
{
    protected $sm;
    protected $authService;
    protected $searchController;
	
    public function __construct(ServiceLocatorInterface $container,\Zend\Authentication\AuthenticationService $authService)
    {
        $this->sm = $container;
        $this->authService = $authService;
    }
	
    public function indexAction()
    {
    	error_log("AdminRosanController - index");
        $view = new ViewModel();
		$username = $this->params()->fromQuery('username', null);
		$password = $this->params()->fromQuery('password', null);
    	if ($username == null && $password == null) {
            $loginform = new LoginForm();
            $view->setVariable('loginform', $loginform); 
            $view->setVariable('valid', true); 
        }
        else {
            $loginform = new LoginForm($username, $password);
            $view->setVariable('loginform', $loginform);
            $view->setVariable('valid', false); 
        }
		return $view;       	
    }
    public function loginAction()
    {        
    	error_log("AdminRosanController - login");
    	$ok = false;
    	$request = $this->getRequest();
        if ($request->isPost()) {
			$data = $request->getPost();
			if (isset($data['submit'])) {
				if (isset($data['username']) && strlen($data['username']) > 0) {
					$username = $data['username'];
    				if (isset($data['password']) && strlen($data['password']) > 0) {
	    				$password = $data['password'];
                        // check
                        $adapter = $this->authService->getAdapter();
                        $adapter->setIdentity($username);
                        $adapter->setCredential($password);

                        $result = $this->authService->authenticate();
                        if (!$result->isValid()) {
                            // Authentication failed; print the reasons why
                            foreach ($result->getMessages() as $message) {
                                error_log( $message);
                            }
                            $this->redirect()->toRoute('adminos',
                                          array('action' => 'index'),
                                          array( 'query' => array(
                                                    'username' => $username)));
                            return;
                        }
                        $ok = true;
    	            }
    	        }
    	    }
    	}
        $view = new ViewModel();
    	if ($ok === true) {
            $fullname = DicUserTable::getFullname($this->sm, $username);
            if ($fullname == null)
                $fullname = "";
            $logoutform = new LogoutForm($fullname);
            $view->setVariable('logoutform', $logoutform);
        }
        else {
            $this->redirect()->toRoute('adminos',
            array('action' => 'index'),
            array( 'query' => array(
                       'username' => $username)));
        }
        
		return $view;       	
    }
    
    public function logoutAction()
    {
        $this->authService->clearIdentity();
        $this->redirect()->toRoute('adminos');
    }
    public function dictAction()
    {
        error_log("AdminRosanController : dictAction");
        if ($this->authService->hasIdentity() === false) {
            $this->redirect()->toRoute('adminos');
        }
        $view = new ViewModel();
       	$wordform = new AdminWordForm();
       	$view->setVariable('wordform', $wordform); 
		return $view;       	
    }
    public function wordAction()
    {
        if ($this->authService->hasIdentity() === false) {
            $this->redirect()->toRoute('adminos');
            return;
        }
        
        error_log("AdminRosanController : wordAction");
        $view = new ViewModel();
       	$wordform = new AdminWordForm();
       	$view->setVariable('wordform', $wordform); 
         if ($this->request->isPost()) {
			$data = $this->request->getPost();
            if (isset($data['delete'])) {
                $id = $this->params()->fromQuery('id', null);
                if ($id != null) {
                    ChangesTable::addToDelete($this->sm, $this->authService, $id);
                    ArticleTables::deleteArticle($this->sm, $id);
                    //error_log($id);
                }
            }   
            else if (isset($data['edit'])) {
                $id = $this->params()->fromQuery('id', null);
                if ($id != null) {
                    $src = ArticleTables::getSrc($this->sm, $id);
                    $src = $this->editPrepare($src);
                    $article = new ViewModel();
		            $editform = new ArticleEditForm($src);
                    $article->setVariable('editform', $editform);
                    $article->setVariable('id', $id);
    		        $article->setTemplate('admin/admin-rosan/edit-article');
	    	        $view->addChild($article, 'article');
                }
            }   
            else if (isset($data['artnew'])) {
                error_log("new-art");
                $article = new ViewModel();
		        $newform = new ArticleNewForm();
                $article->setVariable('newform', $newform);
    		    $article->setTemplate('admin/admin-rosan/new-article');
    		    /*
	    	    $modal = new ViewModel();
    		    $modal->setTemplate('admin/admin-rosan/modal-of');
				$modal->setVariable('orthos', OrthogrTables::getAllOrthogr($this->sm));
    		    
	    	    $article->addChild($modal, 'modal'); */
	    	    $view->addChild($article, 'article');
            }   
            else if (isset($data['editok'])) {
                $id = $this->params()->fromQuery('id', null);
                if ($id != null) {
                    error_log("edit-ok");
                    if (isset($data['article'])) {
                        $text = $data['article'];
                        $text = $this->editCorrect($text);
                        ChangesTable::addEdited($this->sm, $this->authService, $id, $text);
                    }
                }
            }  
            else if (isset($data['newok'])) {
                error_log("new-ok");
                if (isset($data['article'])) {
                    $text = $data['article'];
                    $dic = $data['dic'];
                    $text = $this->editCorrect($text);
                    ChangesTable::addNew($this->sm, $this->authService, $text, $dic);
                }
            } 
			else if (isset($data['submit'])) {
                $articles = SearchController::processRequest($this->sm, $this->getRequest(), $this->params(), $view);
		        $articles->setVariable('route', 'dict');
		        $articles->setVariable('action', 'word');
		        foreach ($articles->articles as $art) {
		            $actionsform = new ArticleActionsForm($art['id']);
                    $articles->setVariable('artform'.strval($art['id']), $actionsform);
        		}
         	    $articles->setVariable('pag_part', 'admin/admin-rosan/paginator1.phtml');
		        $articles->setTemplate('admin/admin-rosan/articles');
		        $view->addChild($articles, 'articles');
            }
            else if (isset($data['addinfo'])) {
                $id = $this->params()->fromQuery('id', null);
                if ($id != null) {
                    $src = ArticleTables::getSrc($this->sm, $id);
                    $src = $this->editPrepare($src);
                    $title = ArticleTables::getTitle($this->sm, $id);
                    $article = new ViewModel();
		            $addinfoform = new ArticleAddInfoForm();
		            $srcs = SourcesTable::getAll($this->sm);
		            $sel = $addinfoform->get('sources');
		            $sel->setValueOptions($srcs);
		            $infos = ArticleAddInfoTable::getAllForArticle($this->sm, $id);
                    $article->setVariable('addinfoform', $addinfoform);
                    $article->setVariable('id', $id);
                    $article->setVariable('title', $title);
                    $article->setVariable('src', $src);
                    $article->setVariable('infos', $infos);
    		        $article->setTemplate('admin/admin-rosan/edit-addinfo');
	    	        $view->addChild($article, 'article');
//	    	        print_r($infos);
                }
            } 
        }
        else {
            $query = $this->params()->fromQuery('query', "-");
    		$page = $this->params()->fromQuery('page', 1);
		
		    $title_check = $this->params()->fromQuery('title_check', 0);
		    $text_check = $this->params()->fromQuery('text_check', 0);

            if (strlen($query) > 0) {
			    $wordform->get('word')->setAttribute('value', $query);
        	    if ($title_check != 0)
				    $wordform->get('title_check')->setAttribute('value', 'yes');
			    else if ($title_check == 0 || !isset($data['title_check']) || $data['title_check'] == "no") {
    				$wordform->get('title_check')->setAttribute('value', 'no');
			    }
			    if ($text_check != 0)
				    $wordform->get('text_check')->setAttribute('value', 'yes');
			    else if ($text_check == 0 || !isset($data['text_check']) || $data['text_check'] == "no") {
    				$wordform->get('text_check')->setAttribute('value', 'no');
			    }
                $articles = SearchController::processRequest($this->sm, $this->getRequest(), $this->params(), $view);
		        $articles->setVariable('route', 'dict');
		        $articles->setVariable('action', 'word');
		        foreach ($articles->articles as $art) {
		            $actionsform = new ArticleActionsForm($art['id']);
                    $articles->setVariable('artform'.strval($art['id']), $actionsform);
        		}
         	    $articles->setVariable('pag_part', 'admin/admin-rosan/paginator1.phtml');
		        $articles->setTemplate('admin/admin-rosan/articles');
		        $view->addChild($articles, 'articles');
            }
        }
		return $view;       	
    }
    public function editAction()
    {
        error_log("AdminRosanController : editAction");
        if ($this->authService->hasIdentity() === false) {
            $this->redirect()->toRoute('adminos');
            return;
        }
        error_log("AdminRosanController : editAction2");
        $view = new ViewModel();
        if ($this->request->isPost()) {
			$data = $this->request->getPost();
            if (isset($data['cancel'])) {
                $id = $this->params()->fromQuery('id', null);
                if ($id != null) {
                	error_log(sprintf("delete item with id = %d", $id));
                    ChangesTable::cancelChange($this->sm, $id);
                }
            }
            else if (isset($data['rebuild'])) {
    //            	error_log(sprintf("start rebuilding"));
                	$changes = ChangesTable::getForProcessing($this->sm);
                	//$filename = '/home/test/Data/preview/pre_in.txt';
                	//$filename = '/ElenaTenkova/ИРЯ/OROSS/oross/changes/in.txt';
                	$filename = '/www/ruslang/oross.ruslang.ru/Data/changes/in.txt';
				    $fp = fopen($filename, 'w');
				    if ($fp) {
				        foreach ($changes as $ch) {
				            $str = "a_c:\t".strval($ch['id']).PHP_EOL;
    					    fwrite($fp, $str);
				            $str = "a_act:\t".strval($ch['action']).PHP_EOL;
    					    fwrite($fp, $str);
				            $str = "a:\t".strval($ch['id_article']).PHP_EOL;
    					    fwrite($fp, $str);
				            $str = "a_dic:\t".strval($ch['dic']).PHP_EOL;
    					    fwrite($fp, $str);
				            $str = "a_text:\t".strval($ch['text']).PHP_EOL;
				            $str = ChangesTable::processDiacritics($str);
    					    fwrite($fp, $str);
    					}
					fclose($fp);
					$infos = ArticleAddInfoTable::getAll($this->sm);
                	$filename = '/www/ruslang/oross.ruslang.ru/Data/changes/addinfo.txt';
				    $fp = fopen($filename, 'w');
				    if ($fp) {
				        foreach ($infos as $info) {
				            $str = "add_id:\t".strval($info['id']).PHP_EOL;
    					    fwrite($fp, $str);
				            $str = "a_id:\t".strval($info['id_article']).PHP_EOL;
    					    fwrite($fp, $str);
    					}
    				}
					fclose($fp);
					StateTable::setInProcessState($this->sm);
					$script =  '/www/ruslang/oross.ruslang.ru/Data/rebuild.sh';
	//				$script =  '/var/www/www-root/data/www/ruslang-oross.ru/getdic/clroutput.sh';
                    shell_exec($script." > /dev/null 2 > /dev/null &");
                    }
            }
        } 
        $state = StateTable::getState($this->sm);
        if (count($state) > 0) {
            $view->setVariable('state', $state);
        }
        if ($state['state'] != StateTable::UnderReconstruction) {
            $rebuildform = new RebuildForm();
            $view->setVariable('rebuildform', $rebuildform);
        }    
//        $changes = ChangesTable::getAll($this->sm);
        $changes = ChangesTable::getForPage($this->sm,  $this->params()->fromQuery('page', 1));
        $view->setVariable('changes', $changes['show']);
        $view->setVariable('paginator', $changes['paginator']);
        $view->setVariable('pageCount', count($changes['paginator']));
        $view->setVariable('pag_part', 'admin/admin-rosan/paginator1.phtml');
		foreach ($changes['show'] as $item) {
		    if ($item['status'] == ChangesTable::Expects) {
			    $cancelform = new ActionCancelForm($item['id']);
    	        $view->setVariable('cancelform'.strval($item['id']), $cancelform);
    	    }
        }
		return $view;       	
    }
    
    private function editPrepare($src)
    {
     	$src = str_replace("<u>", "<span style=\"text-decoration: underline;\">", $src);
        $src = str_replace("</u>", "</span>", $src);
        $src = str_replace("&#x301;", "#", $src);
        //error_log($src);
    	return $src;
    }

    private function editCorrect($text)
    {
    	$underline = "<span style=\"text-decoration: underline;\">";
    	$pos = strpos($text, $underline);
    	while ($pos !== false) {
	     	$text = substr_replace($text, "<u>", $pos, strlen($underline));
    		$pos = strpos($text, "</span>", $pos);
    		if ($pos !== false)
		     	$text = substr_replace($text, "</u>", $pos, strlen("</span>"));
	    	$pos = strpos($text, $underline);
    	}
    	
        $text = str_replace("#page=", "11page22", $text);
        $text = str_replace("#", "&#x301;", $text);
        $text = str_replace("11page22", "#page=", $text);
    	
        //error_log($text);
	    $text = str_replace("<strong>", "<b>", $text);
	    $text = str_replace("</strong>", "</b>", $text);
	    $text = str_replace("<em>", "<i>", $text);
	    $text = str_replace("</em>", "</i>", $text);
		//
	    $text = str_replace("&laquo;", "«", $text);
	    $text = str_replace("&raquo;", "»", $text);
	    $text = str_replace("&rsquo;", "’", $text);
	    $text = str_replace("&loz;", "◊", $text);
	    $text = str_replace("&sect;", "§", $text);
		$text = str_replace("&hellip;", "…", $text);
		$text = str_replace("&ndash;", "–", $text);
		$text = str_replace("&nbsp;", " ", $text);
		$text = str_replace("&sup1;", "¹", $text);
		$text = str_replace("&sup2;", "²", $text);
	    $pos = strpos($text, "<p>");
	    if ($pos == 0) {
		     	$text = substr_replace($text, "", $pos, strlen("<p>"));
	    }
	    $pos = strrpos($text, "</p>");
	    if ($pos !== false) {
		     	$text = substr_replace($text, "", $pos, strlen("</p>"));
	    }
	    // remove special
		//$text = preg_replace("/&#?[a-z0-9]+;/i","",$text);	    
        //error_log($text);
    	return $text;
    }
    
    public function getFormulasAction()
    {
    	error_log("getFormulas Action");
		if (isset($_POST['orth']) && $_POST['orth'] != "0") {
			//error_log($_POST['id_formula']);
			$formulas = FormulaTables::getFormulas($this->getServiceLocator(), $_POST['orth']);
			$result = array();
			foreach ($formulas as $formula) {
				$result[] = array('id' => $formula['id'], 'name' => $formula['name']);
			}
			return new JsonModel(array(
					'formulas' => $result,
					'success' => true,
				));
            }
			return new JsonModel(array(
				'success' => false,
			));
 	}
    public function getdicsAction()
    {
    	error_log("AdminRosanController - getdics");
        if ($this->authService->hasIdentity() === false) {
            $this->redirect()->toRoute('adminos');
            return;
        }
    	
        $view = new ViewModel();
    	$request = $this->getRequest();
        if ($request->isPost()) {
			$data = $request->getPost();
			if (isset($data['oross'])) {
				$script =  '/home/ruslang/ruslang/oross/getdic/get_dic.sh';
//			    $script =  $_SERVER['SERVER_NAME'].'/getdic/get_dic.sh';
//			    $script = '/ElenaTenkova/ИРЯ/OROSS/oross/downloads/get_dic_loc.sh';
//			    error_log($script);
//			    exec($script.' 2>&1', $output);
                shell_exec($script." > /dev/null 2 > /dev/null &");
//print_r($output);  // to see the response to your command
//                exec("textedit");
                DicsTable::updateState($this->getServiceLocator(), 1, DicsTable::Expects, date("Y-m-d"));
            }
			if (isset($data['ros'])) {
				$script =  '/home/ruslang/ruslang/oross/getdic/get_ros.sh';
                shell_exec($script." > /dev/null 2 > /dev/null &");
                DicsTable::updateState($this->getServiceLocator(), 2, DicsTable::Expects, date("Y-m-d"));
            }
        }
		$getdicform = new GetDicForm();
        $view->setVariable('getdicform', $getdicform);
        $dics = DicsTable::getAll($this->sm);
        $view->setVariable('dics', $dics);
        error_log("executed");
		return $view;       	
    }
 	
 	public function previewAction()
    {
        error_log("AdminRosanController : previewAction");
		if (isset($_POST['text'])) {
			$text = $_POST['text'];
			if (strlen($text) != 0) {
            	$text = $this->editCorrect($text);
            	
        		$filename = '/www/ruslang/oross.ruslang.ru/Data/preview/pre_in.txt';
				$fp = fopen($filename, 'w');
				if ($fp) {
					fwrite($fp, $text);
					fclose($fp);
					$script =  '/www/ruslang/oross.ruslang.ru/getdic/preview.sh';
                	shell_exec($script);
	        		$filename = '/www/ruslang/oross.ruslang.ru/Data/preview/pre_out.html';
					$fp = fopen($filename, 'r');
					if ($fp) {
						$text = fread($fp, filesize($filename));
						$text = '<p id="pwtext">'.$text;
						fclose($fp);
						return new JsonModel(array(
							'out' => $text,
							'success' => false,
						));
					}
                }        
			}
        }
		return new JsonModel(array(
			'out' => '',
			'success' => false,
		));
    }

    public function userAction()
    {
        error_log("AdminRosanController : userAction");
        if ($this->authService->hasIdentity() === false) {
            $this->redirect()->toRoute('adminos');
        }
        $view = new ViewModel();
       	$adduserform = new AddUserForm();
       	$view->setVariable('adduserform', $adduserform); 
        if ($this->request->isPost()) {
			$data = $this->request->getPost();
            if (isset($data['submit'])) {
            	$name = isset($data['username']) ? $data['username'] : '';
            	$password = isset($data['password']) ? $data['password'] : '';
            	$fullname = isset($data['fullname']) ? $data['fullname'] : 'username';
            	if ($name != '') {
    	        	if (DicUserTable::userExists($this->sm, $name) === true) {
	            		$view->setVariable('valid', DicUserTable::Exists);
		            	$view->setVariable('username', $name);
		            	return $view;
		            }
	            	else if ($password != '') {
	            		if ($fullname == '')
	            			$fullname = $name;
    	        		DicUserTable::addUser($this->sm, $name, $password, $fullname);
	            		$view->setVariable('valid', DicUserTable::Added);
	            		return $view;
	            	}
	            }
	            if ($name == '' || $password == '') {
            		$view->setVariable('valid', DicUserTable::Incorrect);
	            	$view->setVariable('username', $name);
    	        	$view->setVariable('password', $password);
    	        	$view->setVariable('fullname', $fullname);
    	        }
			}
			else if (isset($data['delete']) && isset($data['username'])) {
				$name = $data['username'];
				if ('username' != '') {
					DicUserTable::deleteUser($this->sm, $name);
		            $view->setVariable('valid', DicUserTable::Deleted);
		         }
		         else
			         $view->setVariable('valid', DicUserTable::None);
			}
		}
		else {
			$view->setVariable('valid', DicUserTable::None);
		}
		return $view;       	
    }
 	public function addsrcAction()
    {
        error_log("AdminRosanController : addsrcAction");
		if (isset($_POST['text'])) {
			$text = $_POST['text'];
			if (strlen($text) != 0) {
			    SourcesTable::add($this->sm, $text);
			    $res = SourcesTable::getAll($this->sm);
			    $result = array();
			    foreach ($res as $id => $val) {
			        $result[] = array('id' => $id, 'val' => $val);
			    }
			    return new JsonModel(array(
			        'src' => $result,
                    'success' => true,
				));
			}
        }
		return new JsonModel(array(
			'success' => false,
		));
    }
 	public function addarticleinfoAction()
 	{
        error_log("AdminRosanController : addarticleinfoAction");
		if (isset($_POST['text']) && isset($_POST['id_article']) && isset($_POST['id_src'])) {
			$text = $_POST['text'];
			$text = $this->editCorrect($text);
			$id_article = $_POST['id_article'];
			$id_src = $_POST['id_src'];
			$id = $_POST['id'];
			error_log($id);
			if ($id == 'FF' && strlen($text) != 0) {
			    $id = ArticleAddInfoTable::add($this->sm, $id_article, $text, $id_src);
			    return new JsonModel(array(
			        'id' => $id,
                    'success' => true,
				));
			}
			else if ($id != "FF" && strlen($text) != 0) {
			    $id = ArticleAddInfoTable::update($this->sm, $id, $text, $id_src);
			    return new JsonModel(array(
			        'id' => $id,
                    'success' => true,
				));
			}
        }
		return new JsonModel(array(
			'success' => false,
		));
 	}
 	public function delarticleinfoAction()
 	{
        error_log("AdminRosanController : delarticleinfoAction");
		if (isset($_POST['id'])) {
		    $id = $_POST['id'];
			if ($id != "FF") {
			    ArticleAddInfoTable::del($this->sm, $id);
			    return new JsonModel(array(
                    'success' => true,
				));
			}
        }
		return new JsonModel(array(
			'success' => false,
		));
 	}
 	
 	public function getinfoblockAction()
 	{
            error_log("AdminRosanController : getinfoblockAction");
            $addinfoform = new ArticleAddInfoForm();
		    $srcs = SourcesTable::getAll($this->sm);
            $fselect = new FormSelect($addinfoform->get('sources')->setAttribute('data-key', 'FF'));
            $str = '<div class="row top-buffer"><div class="col-md-6">';
            $str = $str.sprintf('<select %s>%s</select>',
                $fselect->createAttributesString($addinfoform->get('sources')->setAttribute('data-key', 'FF')->getAttributes()),
                $fselect->renderOptions($srcs, array()));
            $str = $str.'</div><div class="col-md-2"><input id="addsrcbutton" type="button" value="Добавить новый источник" name="addsrc" data-key=\'FF\' data-toggle="modal" data-target="#srcModal" /></div></div><div class="row top-buffer"><div class="col-md-12"><textarea name="info" id="info_FF" ></textarea></div></div><div class="row top-buffer"><div class="col-md-6"><input type="button" value="Сохранить" data-key="FF" onclick="addNewInfo(this)">&nbsp;<input name="delinfo" type="button" id="delinfobutton"  data-key="FF" value="Удалить" onclick="delNewInfo(this)"></div></div>';
        return new JsonModel(array(
            'result' => $str,
            'success' => true,
		));
 	}
 	
}
