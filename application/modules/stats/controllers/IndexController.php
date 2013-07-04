<?php
class Stats_IndexController extends Zend_Controller_Action {
	
	protected $_arrParam;
	public function init(){
		$this->_arrParam = $this->_request->getParams();
		$this->getHelper('viewRenderer')->setNoRender();
		$this->_helper->layout->disableLayout();
	}
	
	public function indexAction()
	{		
			
	}
	
	public function statsupdateAction()
	{
		$page_url = trim($this->_arrParam['url']);		
		$route = Zend_Controller_Front::getInstance()
                              ->getRouter()
                              ->getRoute( 'article-index-detail' );
        $params = $route->match( $page_url );
        $article_id = intval($params['id']);
        if($article_id && $params['module'] == 'article' && $params['action'] == 'detail')
        {        	
        	$article_model 	= new Article_Model_Article();
        	$article 		= $article_model->getItem(array('id'=>$article_id),array('task'=>'admin-info'));	
        	if($article)
        	{
        		
        		$facebook_graph = "https://graph.facebook.com/$page_url";        		
        		$json = json_decode(file_get_contents($facebook_graph));        	
        		if($json->shares || $json->comments)
        		{
        			$stats_model = new Stats_Model_Stats();
        			$stats_model->updateStats(array('article_id'=>$article_id,'user_id'=>$article['user_id'],'like_count'=>$json->shares,'comment_count'=>$json->comments));        			
        		}        		
        	}
        }                        
		//
	}
	
	
	
}

?>