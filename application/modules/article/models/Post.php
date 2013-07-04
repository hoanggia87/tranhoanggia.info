<?php
class Article_Model_Post extends Zend_Db_Table{
	protected $_name = 'article_detail';
	protected $_primary = 'id';
	public function saveItem($arrParam = null, $options = null)
    {
		if($options['task'] == 'user-add-image')
        {
			$row =  $this->fetchNew();
			
			$row->page_id 		= 0;
			
			$row->status 	= 1 ; 			
  			
  			$row->link 			= '';
  			$row->create_date 		= date('Y-m-d H:i:s');

  			$auth = Zend_Auth::getInstance();
  			$infoAuth = $auth->getIdentity();

  			$row->user_id 	= $infoAuth->user_id;
  			$row->title 		= $arrParam['title'];
  			
  			$row->description 			= '';
  			$row->image 		= $arrParam['image'];
            $row->source 		= $arrParam['source'];
  			$row->video		= '';
            $row->type		= 'image';
  			
  			$row->is_hot		= 0;
            $row->is_adult		= $arrParam['is_adult'];
  			
  			$row->cat_id		= 1;
			return $row->save();
			
		}
		if($options['task'] == 'user-add-video')
        {
			$row =  $this->fetchNew();
			
			$row->page_id 		= 0;
			
			$row->status 	= 1 ; 			
  			
  			$row->link 			= '';
  			$row->create_date 		= date('Y-m-d H:i:s');

  			$auth = Zend_Auth::getInstance();
  			$infoAuth = $auth->getIdentity();

  			$row->user_id 	= $infoAuth->user_id;
  			$row->title 		= $arrParam['title'];
  			
  			$row->description 			= '';
  			$row->image 		= $arrParam['image'];
            $row->video 		= $arrParam['video'];
            $row->source 		= $arrParam['source'];
            $row->type		= 'video';
  			
  			$row->is_hot		= 0;
            $row->is_adult		= $arrParam['is_adult'];
  			
  			$row->cat_id		= 1;
			return $row->save();
			
		}
        if($options['task'] == 'user-add-truyencuoi')
        {
			$row =  $this->fetchNew();
			
			$row->page_id 		= 0;
			
			$row->status 	= 1 ; 			
  			
  			$row->link 			= '';
  			$row->create_date 		= date('Y-m-d H:i:s');

  			$auth = Zend_Auth::getInstance();
  			$infoAuth = $auth->getIdentity();

  			$row->user_id 	= $infoAuth->user_id;
  			$row->title 		= $arrParam['title'];
  			
  			$row->description 			= '';
  			$row->image 		= '';
            $row->video 		= '';
            $row->source 		= $arrParam['source'];
            $row->description 		= $arrParam['content'];
            $row->type		= 'blog';
  			
  			$row->is_hot		= 0;
            $row->is_adult		= $arrParam['is_adult'];
  			
  			$row->cat_id		= 2;
			return $row->save();
			
		}
        if($options['task'] == 'user-add-tamsu')
        {
			$row =  $this->fetchNew();
			
			$row->page_id 		= 0;
			
			$row->status 	= 1 ; 			
  			
  			$row->link 			= '';
  			$row->create_date 		= date('Y-m-d H:i:s');

  			$auth = Zend_Auth::getInstance();
  			$infoAuth = $auth->getIdentity();

  			$row->user_id 	= $infoAuth->user_id;
  			$row->title 		= $arrParam['title'];
  			
  			$row->description 			= '';
  			$row->image 		= '';
            $row->video 		= '';
            $row->source 		= $arrParam['source'];
            $row->description 		= $arrParam['content'];
            $row->type		= 'blog';
  			
  			$row->is_hot		= 0;
            $row->is_adult		= $arrParam['is_adult'];
  			
  			$row->cat_id		= 3;
			return $row->save();
			
		}
        if($options['task'] == 'user-add-baohay')
        {
			$row =  $this->fetchNew();
			
			$row->page_id 		= 0;
			
			$row->status 	= 1 ; 			
  			
  			$row->link 			= '';
  			$row->create_date 		= date('Y-m-d H:i:s');

  			$auth = Zend_Auth::getInstance();
  			$infoAuth = $auth->getIdentity();

  			$row->user_id 	= $infoAuth->user_id;
  			$row->title 		= $arrParam['title'];
  			
  			$row->description 			= '';
  			$row->image 		= '';
            $row->video 		= '';
            $row->source 		= $arrParam['source'];
            $row->description 		= $arrParam['content'];
            $row->type		= 'blog';
  			
  			$row->is_hot		= 0;
            $row->is_adult		= $arrParam['is_adult'];
  			
  			$row->cat_id		= 4;
			return $row->save();
			
		}		
	}
    public function youtube_id_from_url($url) 
    {
        $video_id = false;
        $url = parse_url($url);
        if (strcasecmp($url['host'], 'youtu.be') === 0)
        {
            #### (dontcare)://youtu.be/<video id>
            $video_id = substr($url['path'], 1);
        }
        elseif (strcasecmp($url['host'], 'www.youtube.com') === 0)
        {
            if (isset($url['query']))
            {
                parse_str($url['query'], $url['query']);
                if (isset($url['query']['v']))
                {
                    #### (dontcare)://www.youtube.com/(dontcare)?v=<video id>
                    $video_id = $url['query']['v'];
                }
            }
            if ($video_id == false)
            {
                $url['path'] = explode('/', substr($url['path'], 1));
                if (in_array($url['path'][0], array('e', 'embed', 'v')))
                {
                    #### (dontcare)://www.youtube.com/(whitelist)/<video id>
                    $video_id = $url['path'][1];
                }
            }
        }
        return $video_id;
    }
}