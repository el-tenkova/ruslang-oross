<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/ZendSkeletonApplication for the canonical source repository
 * @copyright Copyright (c) 2005-2015 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */

namespace Application\Controller;

 use Contents\Model\ParagraphTables;
 use Contents\Model\RuleTables;
 use Contents\Model\FootNoteTables;
 use Contents\Model\WordTables;
 
 use Zend\Mvc\Controller\AbstractActionController;

 use Zend\View\Model\ViewModel;
 use Zend\View\Model\JsonModel;

 use Zend\Db\Sql\Sql;
 use Zend\Db\Adapter\Adapter;

 class ParagraphController extends AbstractActionController
 {
     
     public function viewAction()
     {
		error_log("Paragraph viewAction");
		
        $view = new ViewModel();
		
		if ($this->params('id') !== null) {
			$id_para = $this->params('id');
			$para = new ViewModel(array('id' => $id_para,
										'title' => ParagraphTables::getParaTitle($this->getServiceLocator(), $id_para),
		                            	'rules' => RuleTables::getRulesForPara($this->getServiceLocator(), $id_para),
		                            	'footnotes' => FootNoteTables::getFootForPara($this->getServiceLocator(), $id_para)));
		                                
        	$para->setTemplate('contents/paragraph');
	        $view->addChild($para, 'paragraph');
		}
        
        return $view;        
        
     }

     public function paraAction()
     {
		error_log("Paragraph paraAction");
		
        if (isset($_POST['id'])) {
            //error_log($_POST['id']);
            $title = ParagraphTables::getParaTitle($this->getServiceLocator(), $_POST['id']);
            $rules = RuleTables::getRulesForPara($this->getServiceLocator(), $_POST['id']);
            if ($title !== false) {
                $result = new JsonModel(array(
                    "title" => $title,
                    "rules" => $rules,
                    "success" => true,
                ));
                return $result;
            }
        }
        return new JsonModel(array(
                    'success' => false,
                ));
     }

     public function ruleAction()
     {
		error_log("Paragraph ruleAction");
		
        if (isset($_POST['id'])) {
         //   error_log($_POST['id']);
//            $title = ParagraphTables::getParaTitle($this->getServiceLocator(), $_POST['id']);
            $rule = RuleTables::getRuleFull($this->getServiceLocator(), $_POST['id']);
            if (count($rule) != 0) {
                $result = new JsonModel(array(
//                    "title" => $title,
                    "rule" => $rule,
                    "success" => true,
                ));
                return $result;
            }
        }
        return new JsonModel(array(
                    'success' => false,
                ));
     }

     public function markedruleAction()
     {
		error_log("Paragraph  MARKED ruleAction");
		
        if (isset($_POST['id'])) {
         //   error_log($_POST['id']);
//            $title = ParagraphTables::getParaTitle($this->getServiceLocator(), $_POST['id']);
		//	error_log($_POST['query']);
			if (isset($_POST['query'])) {
				$marks = WordTables::getTutorial($this->getServiceLocator(), $_POST['query'], 1, $_POST['id']);
				$rule = RuleTables::getRuleFull($this->getServiceLocator(), $_POST['id']);
				if (count($marks) > 0) {
		//			error_log("count marks > 0");
					$mark_before = "<span class=\"marked\" >";
					$mark_after = "</span>";
					$mark_len = strlen($mark_before) + strlen($mark_after);
					$offset = 0;					
					$text_len = strlen($rule['text']);
					foreach ($marks[0]['marks'] as $mark) {
//						if ($mark['type'] == 4) {
							if ($mark['start'] + $mark['len'] <= $text_len) {
								$rule['text'] = substr_replace($rule['text'], $mark_before, $mark['start'] + $offset, 0);
								$rule['text'] = substr_replace($rule['text'], $mark_after, $mark['start'] + $mark['len'] + $offset + strlen($mark_before), 0);
		//								error_log($article->text);
								$offset += $mark_len;
							}
//							else {
//								break;
//							}
					//	}
					}
					$offset = 0;
					foreach ($marks[0]['marks'] as $mark) {
						//if ($mark['type'] == 4) {
							if ($mark['start'] + $mark['len'] > $text_len) {
								$rule['info'] = substr_replace($rule['info'], $mark_before, $mark['start'] - $text_len + $offset, 0);
								$rule['info'] = substr_replace($rule['info'], $mark_after, $mark['start'] - $text_len + $mark['len'] + $offset + strlen($mark_before), 0);
		//								error_log($article->text);
								$offset += $mark_len;
							}
						//}
					}
					foreach ($rule['orthos'] as &$ortho) {
						if (isset($marks[0][WordTables::$types[WordTables::typeOrtho]['sign']])) {
							foreach ($marks[0][WordTables::$types[WordTables::typeOrtho]['sign']] as $ortho_mark) {
								if ($ortho_mark['id'] == $ortho['id_ortho']) {
									$offset = 0;
									foreach ($ortho_mark['marks'] as $om) {
										$ortho['ortho'] = substr_replace($ortho['ortho'], $mark_before, $om['start'] + $offset, 0);
										$ortho['ortho'] = substr_replace($ortho['ortho'], $mark_after, $om['start'] + $om['len'] + $offset + strlen($mark_before), 0);
										$offset += $mark_len;
									}
								}
							}
						}		
						if (isset($marks[0][WordTables::$types[WordTables::typeFormula]['sign']])) {
//							error_log("add mark to formula");
							foreach ($marks[0][WordTables::$types[WordTables::typeFormula]['sign']] as $formula_mark) {
								if ($formula_mark['id'] == $ortho['id_form']) {
									$offset = 0;
									foreach ($formula_mark['marks'] as $fm) {
										$ortho['formula'] = substr_replace($ortho['formula'], $mark_before, $fm['start'] + $offset, 0);
										$ortho['formula'] = substr_replace($ortho['formula'], $mark_after, $fm['start'] + $fm['len'] + $offset + strlen($mark_before), 0);
										$offset += $mark_len;
									}
								}
							}
						}					
						if (isset($marks[0][WordTables::$types[WordTables::typeFormulaExample]['sign']])) {
							foreach ($marks[0][WordTables::$types[WordTables::typeFormulaExample]['sign']] as $example_mark) {
								if ($example_mark['id'] == $ortho['id_form']) {
							//error_log('example');
									$offset = 0;
									foreach ($example_mark['marks'] as $em) {
										$ortho['example'] = substr_replace($ortho['example'], $mark_before, $em['start'] + $offset, 0);
										$ortho['example'] = substr_replace($ortho['example'], $mark_after, $em['start'] + $em['len'] + $offset + strlen($mark_before), 0);
										$offset += $mark_len;
									}
								}
							}
						} 
					} 
					foreach ($rule['footnotes'] as &$foot) {
						//error_log('footnotes');					
						if (isset($marks[0][WordTables::$types[WordTables::typeFootNote]['sign']])) {
							foreach ($marks[0][WordTables::$types[WordTables::typeFootNote]['sign']] as $foot_mark) {
								if ($foot_mark['id'] == $foot['id']) {
									$offset = 0;
									foreach ($foot_mark['marks'] as $fm) {
										$foot['footnote'] = substr_replace($foot['footnote'], $mark_before, $fm['start'] + $offset, 0);
										$foot['footnote'] = substr_replace($foot['footnote'], $mark_after, $fm['start'] + $fm['len'] + $offset + strlen($mark_before), 0);
										$offset += $mark_len;
									}
								}
							}
						}					
					}
				}
			}
			else {
				$rule = RuleTables::getRuleFull($this->getServiceLocator(), $_POST['id']);
			}
            if (count($rule) != 0) {
                $result = new JsonModel(array(
//                    "title" => $title,
                    "rule" => $rule,
                    "success" => true,
                ));
                return $result;
            }
        }
        return new JsonModel(array(
                    'success' => false,
                ));
     }
     
 }
?>