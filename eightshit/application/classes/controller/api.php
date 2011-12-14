<?php

class Controller_Api extends Controller {

// APIs for a mobile app to hit

	public function action_index()
	{
		$this->response->body('f off');
	}

	public function action_dick_hero()
	{
		if(empty($_POST['username']) || empty($_POST['userid']) || empty($_POST['oauth_token']) || empty($_POST['oauth_secret']) || empty($_FILES['picture']))
		{
			$this->response->body(json_encode(array('success' => false, 'error' => 'missing credentials')));
		}
		else
		{
			try
			{
				$user = DB::select()->from('users')->where('screenname', '=', $_POST['username'])->limit(1)->execute();
				$user = $user[0];
			}
			catch(Exception $e)
			{
				$user = NULL;
			}

			if($user === NULL)
			{
				$details = DB::insert('users', array('screenname', 'userid', 'token', 'secret'))
						->values(array($_POST['username'], $_POST['userid'], $_POST['oauth_token'], $_POST['oauth_secret']))
						->execute();
						
				$user = DB::select()->from('users')->where('id', '=', $details['insert_id'])->execute();
				$user = $user[0];
			}
			else
			{
				DB::update('users')
					->set(array('token' => $_POST['oauth_token'], 'secret' => $_POST['oauth_secret']))
					->where('id', '=', $user['id'])
					->execute();
			}

			$filename = time().$_FILES['picture']['name'];

			try
			{
				Image::factory($_FILES['picture']['tmp_name'])->resize(128, 128)->save(APPPATH.DIRECTORY_SEPARATOR.'incoming/'.$filename);
			}
			catch(Kohana_Exception $e)
			{
				die(json_encode(array('success' => false, 'error' => 'not a valid image')));
			}

			DB::insert('pending_images', array('user_id', 'image', 'from_iphone'))->values(array($user['id'], $filename, '1'))->execute();

			$this->response->body(json_encode(array('success' => true)));
		}
	}

	public function action_mouth_piss()
	{
		$info = $this->request->post();

		$opt_out = (isset($info['wantAvatar']) && ($info['wantAvatar'] == 1)) ? 0 : 1;
		$can_tweet = (isset($info['tweet']) && ($info['tweet'] == 1)) ? 1 : 0;

		if(empty($info['username']))
		{
			throw new Http_Exception_400('No Username');
		}

		DB::update('users')
			->set(array('from_iphone' => 1, 'say_shit' => $can_tweet, 'no_pic' => $opt_out))
			->where('screenname', '=', $info['username'])
			->execute();

		$this->response->body(json_encode(array('success' => true)));
	}

	public function action_list_me()
	{
		$page = Arr::get($_GET, 'page', 0);
		$per_page = Arr::get($_GET, 'per_page', 50);

		$output = array();

		$total_count = DB::select(DB::expr('COUNT(*) as c'))->from('users')->execute()->get('c');

		$total_pages = $total_count % $per_page;

		if (($total_count - ($total_pages*$per_page)) > 0)
		{
			$total_pages++;
		}

		$images = DB::select('id', 'screenname', 'image')
				->from('users')
				->order_by('last_updated', 'DESC')
				->offset($page * $per_page)
				->limit(25)
				->execute()
				->as_array();

		$output['images'] = $images;
		
		$output['total_pages'] =  $total_pages;

		$this->response->headers('content-type', 'application/json');
		$this->response->body(json_encode($output));
	}

}
