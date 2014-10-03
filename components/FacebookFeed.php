<?php

namespace Inkrement\FacebookFeed\Components;

use Cache;
use Request;
use Cms\Classes\ComponentBase;
use System\Classes\ApplicationException;

use Inkrement\FacebookFeed\Models\Settings;

use Facebook;
use Facebook\FacebookSDKException;
use Facebook\FacebookRequestException;
use Facebook\FacebookRequest;
use Facebook\GraphUser;
use Facebook\FacebookSession;
use FacebookAuthorizationException;

class FacebookFeed extends ComponentBase{
    public function componentDetails(){
        return [
            'name'        => 'facebook feed',
            'description' => 'Outputs the configured facebook feed'
        ];
    }

    public function defineProperties(){
        return [
        	'maxItems' => [
        		'title'			=> 'Max posts',
        		'description'	=> 'maximal number of facebook posts to load',
        		'default'		=> 8,
        		'type'			=> 'string'
        	],
        	'maxDescription' => [
        		'title'			=> 'Max description length',
        		'description'	=> 'Each facebook post has a description. Here you can enter a maximal length (the rest will be cropped)',
        		'default'		=> 100,
        		'type'			=> 'string'
        	]
        ];
    }


	public function info(){

		FacebookSession::setDefaultApplication(
			$this->appId, 
			$this->appSecret
		);
		
		try{
			$session = FacebookSession::newAppSession();
		} catch(FacebookSDKException $e){
			return ['message' => 'could not start fb session'];
		}
		
		if($session){
			$request = new FacebookRequest(
			  $session,
			  'GET',
			  '/'.$this->pageId.'/feed'
			);

			$result = [];
			
			try{
				$response = $request->execute();
				$posts = $response->getGraphObject()->asArray()['data'];

				$num = 1;

				foreach ($posts as &$post){

					// load only a limited number of posts (maxItems) 
					if($this->property('maxItems') < $num++) break;


					if (isset($post->object_id))
						$post->image_link = $this->getPictureLink($post->object_id, $session);

					if (isset($post->message))
						$post->short = substr ($post->message, 0, $this->property('maxDescription')).'...';
					else
						$post->short = '';

					array_push($result, $post);
				}
			
				return $result;
			} catch (FacebookRequestException $e){
				return ['message' => 'could not authorize request. please check your facebook AppID, page id and secret!'];
			}
		}

		return ['message' => 'could not load feed'];
	}

	private function getPictureLink($objectId, $session){
		
		if($session){
			$request = new FacebookRequest(
			  $session,
			  'GET',
			  "/$objectId".'/picture',
			  array (
				'redirect' => false,
				'height' => '200',
				'type' => 'normal',
				'width' => '200',
			  )
			);
			try{
				$response = $request->execute();
				$graph = $response->getGraphObject()->asArray();

				return $graph['url'];
			} catch (FacebookRequestException $e){
				return '';
			}
		}
		
		return '';
	}

	public function onRun(){
		$this->appId = Settings::get('fb_app_id');
		$this->appSecret = Settings::get('app_secret');
		$this->pageId = Settings::get('page_id');

		$this->page['facebookfeed'] = $this->info();
	}

}

?>