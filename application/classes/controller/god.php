<?php defined('SYSPATH') or die('No direct access allowed.');

// This is the main admin controller

require(Kohana::find_file('vendor', 'twitter'));

class Controller_God extends Controller {

	public function before()
	{
		if(Auth::instance()->logged_in() === FALSE && $this->request->action() != 'login')
		{
			$this->request->redirect('http://eightshit.me/index.php/god/login');
		}

		if(Auth::instance()->logged_in() === TRUE && $this->request->action() == 'login')
		{
			$this->request->redirect('http://eightshit.me/index.php/god/main');
		}
	}

	public function action_login()
	{
		if(isset($_POST['username']) && isset($_POST['password']))
		{
			if(Auth::instance()->login($_POST['username'], $_POST['password']))
			{
				$this->request->redirect('http://eightshit.me/index.php/god/main');
			}
			else
			{
				$error = 'why the hell';
			}
		}

		$this->view = View::factory('god/login');

		if(isset($error))
		{
			$this->view->error = $error;
		}

		$this->response->body($this->view->render());
	}

	public function action_logout()
	{
		Auth::instance()->logout();
		$this->request->redirect('http://eightshit.me');
	}

	public function action_main()
	{
		$layout = View::factory('layout_god');
		$layout->content = View::factory('god/main');
		$this->response->body($layout->render());
	}

	public function action_queue()
	{
		$layout = View::factory('layout_god');
		$layout->content = View::factory('god/queue');

		$layout->content->queue = DB::select(DB::expr('users.screenname AS creator_name, pending_images.*'))
			->from('pending_images')
			->join('users')->on('users.id', '=', 'pending_images.user_id')
			->execute();

		$this->response->body($layout->render());
	}

	public function action_img_preview()
	{
		try
		{
			$img = DB::select('image')->from('pending_images')->where('id', '=', $this->request->param('id'))->limit(1)->execute()->get('image');

			$this->response->headers('Content-Type', 'image/png');
			$this->response->body(Image::factory(APPPATH.'/incoming/'.$img)->render('png'));
		}
		catch(Exception $e)
		{
			$this->response->status(404);
		}
	}

	public function action_accept()
	{
		$img = DB::select(DB::expr('users.screenname AS artist_name, pending_images.*'))
			->from('pending_images')
			->join('users')->on('users.id', '=', 'pending_images.user_id')
			->where('pending_images.id', '=', $this->request->param('id'))
			->execute();
		$img = $img[0];

		if($img == NULL)
		{
			$this->response->status(404);
		}
		else
		{
			if(isset($_GET['username']) && strlen($_GET['username']) > 1)
			{
				$new_user = DB::select()->from('users')->where(DB::expr('LOWER(screenname)'), '=', strtolower($_GET['username']))->limit(1)->execute();
			}
			else
			{
				$new_user = DB::select()->from('users')->where('image', 'IS', DB::expr('NULL'))->where('no_pic', '=', '0')->order_by('id', 'ASC')->limit(1)->execute();
			}
			$new_user = $new_user[0];

			if($new_user != NULL)
			{
				$original_path = APPPATH.'/incoming/'.$img['image'];
				$new_path = APPPATH.'../avatars/'.$img['image'];

				Image::factory($original_path)->resize(128, 128)->save($new_path);

				DB::update('users')->set(array('image' => $img['image'], 'artist_id' => $img['user_id'], 'from_iphone' => $img['from_iphone']))->where('id', '=', $new_user['id'])->execute();

				$twitter = new Twitter(Kohana::config('twitter.consumerToken'), Kohana::config('twitter.consumerSecret'));

				$twitter->setOAuthToken($new_user['token']);
				$twitter->setOAuthTokenSecret($new_user['secret']);

				$imageFailed = FALSE;

				if(FALSE) // This used to be a condition I used to clean up a big mess I made
				{
					$imageFailed = TRUE;
				}
				else
				{

					try
					{
						$credentials = $twitter->accountVerifyCredentials();
					}
					catch (Exception $e)
					{
						$credentials = array();
					}

					if(isset($credentials['screen_name']))
					{
						try
						{
							$imgUpdate = $twitter->accountUpdateProfileImage($new_path);
						}
						catch(Exception $e)
						{
							$imageFailed = TRUE;
						}

						if($imageFailed == FALSE)
						{
							if($new_user['say_shit'] != 0) // Was once a flag to see if you can be vulgar, now its just to see if you can post
							{
								$twitter->statusesUpdate("wow! @{$img['artist_name']} drew me a cool avatar - I'm going @EightShit !!! http://eightshit.me #eightshit");
							}
							else
							{
								// $twitter->statusesUpdate("wow! @{$img['artist_name']} drew me a cool avatar!!! http://bit.ly/hQrJ7v");
							}

							$bot = new Twitter(Kohana::config('twitter.consumerToken'),  Kohana::config('twitter.consumerSecret'));
							$bot->setOAuthToken(Kohana::config('eightshit.bot.token'));
							$bot->setOAuthTokenSecret(Kohana::config('eightshit.bot.secret'));

							$bot->statusesUpdate("@{$img['artist_name']} just drew @{$new_user['screenname']} a great avatar!! #eightshit");
						}

						try
						{
							// $twitter->friendshipsCreate('');
						}
						catch(Exception $e)
						{

						}

						DB::update('users')->set(array('image_set' => (($imageFailed === FALSE) ? '1' : '0'), 'last_updated' => DB::expr('CURRENT_TIMESTAMP()')))->where('id', '=', $new_user['id'])->execute();
					}
					else
					{
						$imageFailed = TRUE;
					}

				}

				if($imageFailed)
				{
					$tw2 = new Twitter(Kohana::config('twitter.consumerToken'),  Kohana::config('twitter.consumerSecret'));
					$tw2->setOAuthToken(Kohana::config('eightshit.bot.token'));
					$tw2->setOAuthTokenSecret(Kohana::config('eightshit.bot.secret'));
					if($new_user['say_shit'] != 0)
					{
						$tw2->statusesUpdate("@{$new_user['screenname']} twitter auth failed, @{$img['artist_name']} drew your image: http://eightshit.me/index.php/reclaim - love you");
					}
					else
					{
						$tw2->statusesUpdate("@{$new_user['screenname']} twitter auth failed, @{$img['artist_name']} drew your image: http://bit.ly/gZG2qI - love you");
					}
				}

				unlink(APPPATH.'/incoming/'.$img['image']);
				DB::delete('pending_images')->where('id', '=', $img['id'])->execute();

				$this->response->body('true');
			}
		}
	}

	public function action_deny()
	{
		$img = DB::select()->from('pending_images')->where('id', '=', $this->request->param('id'))->execute();
		$img = $img[0];

		if($img == NULL)
		{
			$this->response->status(404);
		}
		else
		{
			unlink(APPPATH.'/incoming/'.$img['image']);
			DB::delete('pending_images')->where('id', '=', $img['id'])->execute();
		}
	}
}
