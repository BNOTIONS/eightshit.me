<?php defined('SYSPATH') or die('No direct script access.');

require(Kohana::find_file('vendor', 'twitter'));

class Controller_Welcome extends Controller {

	public function action_index()
	{
		$layout = View::factory('layout');
		$layout->content = View::factory('index');

		$layout->content->total = DB::select(DB::expr('COUNT(*) as cnt'))->from('users')->where('image', 'IS NOT', DB::expr('NULL'))->execute()->get('cnt');

		$pagination = Pagination::factory(array(
			'total_items' => $layout->content->total,
			'items_per_page' => 500
		));

		$layout->content->guys = DB::select()->from('users')
				->order_by('last_updated', 'DESC')
				->where('image', 'IS NOT', DB::expr('NULL'))
				->limit($pagination->items_per_page)
				->offset($pagination->offset)
				->execute();

		$layout->content->pagi = $pagination->render();
		
		// $layout->content->waiting = DB::select(DB::expr('COUNT(*) as cnt'))->from('users')->where('image', 'IS', DB::expr('NULL'))->execute()->get('cnt');
		// $layout->content->waittime = Kohana::config('eightshit.wait_time');
		$this->response->body($layout->render());
	}

	public function action_twitAuth()
	{
		$twitter = new Twitter(Kohana::config('twitter.consumerToken'),  Kohana::config('twitter.consumerSecret'));

    // XXX: You'll wanna remove all these hardcoded URLs

		$twitter->oAuthRequestToken('http://eightshit.me/index.php/twitAuth'.(isset($_GET['artist']) ? '?artist=yea' : '').(isset($_GET['reclaim']) ? '?reclaim=yea' : ''));

		if (!isset($_GET['oauth_token']))
		{
			$twitter->oAuthAuthorize();
		}
		else
		{
			$token = $twitter->oAuthAccessToken($_GET['oauth_token'], $_GET['oauth_verifier']);
			try
			{
				// Store shit in the DB
				if (empty($token) || empty($token['oauth_token']) || empty($token['oauth_token_secret']) || empty($token['user_id']) || empty($token['screen_name']))
				{
					die('weird, try one more time');
				}
				else
				{
					Session::instance()->set('twitter_screenname', $token['screen_name']);
				}

				DB::insert('users', array('token', 'secret', 'userid', 'screenname', 'say_shit'))->values(array($token['oauth_token'], $token['oauth_token_secret'], $token['user_id'], $token['screen_name'], '1'))->execute();
			}
			catch (Exception $e)
			{

				DB::update('users')
					->set(array('token' => $token['oauth_token'], 'secret' => $token['oauth_token_secret']))
					->where('screenname', '=', $token['screen_name'])
					->execute();

				$user = DB::select()
					->from('users')
					->where('screenname', '=', $token['screen_name'])
					->limit(1)
					->execute();

				$user = $user[0];


				if($user['image_set'] != 1 && $user['image'] != NULL)
				{

					try
					{
						$artist = DB::select()
							->from('users')
							->where('id', '=', $user['artist_id'])
							->limit(1)
							->execute();
						
						$artist = $artist[0];
					}
					catch(Exception $e)
					{
						$artist = NULL;
					}

					$twitter->setOAuthToken($user['token']);
					$twitter->setOAuthTokenSecret($user['secret']);

					$imageFailed = FALSE;

					try
					{
						$twitter->accountUpdateProfileImage(APPPATH.'../avatars/'.$user['image']);
					}
					catch(Exception $e)
					{
						$imageFailed = TRUE;
					}

					if($imageFailed == FALSE)
					{
						if(is_array($artist))
						{
							if($user['say_shit'] != 0)
							{
								$twitter->statusesUpdate("wow! @{$artist['screenname']} drew me a cool avatar - I'm going @EightShit !!! http://eightshit.me #eightshit");
							}
							else
							{
								// $twitter->statusesUpdate("wow! @{$artist['screenname']} drew me a cool avatar !!! http://bit.ly/hQrJ7v");
							}
						}
						else
						{
							if($user['say_shit'] != 0)
							{
								$twitter->statusesUpdate("wow! look at my cool avatar - I'm going @EightShit !!! http://eightshit.me #eightshit");
							}
							else
							{
								// $twitter->statusesUpdate("wow! look at my cool avatar !!! http://bit.ly/hQrJ7v");
							}
						}

						$bot = new Twitter(Kohana::config('twitter.consumerToken'),  Kohana::config('twitter.consumerSecret'));
						$bot->setOAuthToken(Kohana::config('eightshit.bot.token'));
						$bot->setOAuthTokenSecret(Kohana::config('eightshit.bot.secret'));

						$bot->statusesUpdate("@{$artist['screenname']} just drew @{$user['screenname']} a great avatar!! #eightshit");
					}

					try
					{
						$twitter->friendshipsCreate('257825802');
					}
					catch(Exception $e)
					{

					}

					DB::update('users')->set(array('image_set' => '1', 'active_creds' => '1'))->where('id', '=', $user['id'])->execute();

					if($imageFailed)
					{
						$tw2 = new Twitter(Kohana::config('twitter.consumerToken'),  Kohana::config('twitter.consumerSecret'));
						$tw2->setOAuthToken(Kohana::config('eightshit.bot.token'));
						$tw2->setOAuthTokenSecret(Kohana::config('eightshit.bot.secret'));
						if(is_array($artist))
						{
							$tw2->statusesUpdate("@{$user['screenname']} twitter auth failed, @{$user['artist_name']} drew your image: http://eightshit.me/avatars/" . $user['image'] . " - love you");
						}
						else
						{
							$tw2->statusesUpdate("@{$user['screenname']} twitter auth failed, here's your image: http://eightshit.me/avatars/" . $user['image'] . " - love you");
						}
					}

					if(!isset($_GET['artist']))
					{
						die("<html><body><script>alert('Awesome! Your avatar has been set!');window.opener.dickCallback();</script></body></html>");
					}

				}

				if(isset($_GET['reclaim']))
				{
					die("<html><body><script>alert('Thanks but you dont have an image waiting I guess');window.opener.dickCallback();</script></body></html>");
				}

				if(!isset($_GET['artist']))
				{
					$this->response->body('<h1>OOPS</h1> <p>We think you\'ve already signed up - either you already have a picture or you are waiting for us to draw you one. Just hold on we are trying to draw fast, we are looking into getting robot hands</p>');
					$error = true;
				}
			}

			if(!isset($_GET['artist']))
			{
				try
				{
					$imgs = DB::select('waiting_images')->limit(1)->execute();
					foreach ($imgs as $img)
					{
						DB::update('users')->set(array('image' => $img['image']))->where('userid', '=', $token['user_id'])->execute();
						DB::delete('waiting_images')->where('image', '=', $img['image'])->execute();
						$twitter = new Twitter(Kohana::config('twitter.consumerToken'),  Kohana::config('twitter.consumerSecret'));

						$twitter->setOAuthToken($token['oauth_token']);
						$twitter->setOAuthTokenSecret($token['oauth_token_secret']);

						try
						{
							$twitter->accountUpdateProfileImage($img['image']);
						}
						catch (Exception $e)
						{
							$imageFailed = TRUE;
						}

						try
						{
							if($user['say_shit'] != 0)
							{
								$twitter->statusesUpdate('check out my great avatar - I just @EightShit !!! http://eightshit.me #eightshit');
							}
						}
						catch(Exception $e)
						{

						}
						
						try
						{
							// $twitter->friendshipsCreate(''); Makes you friends with a twitter acct, was eightshit
						}
						catch (Exception $e)
						{

						}
					}
				}
				catch (Exception $e)
				{

				}
			}
			if (!isset($error))
				$this->response->body('<html><head><script>window.opener.dickCallback()</script></head></html>');
		}
	}

	public function action_corns()
	{
		if (empty($_POST['user']))
		{
			die('oops');
		}

		$user = DB::select()->from('users')->where('screenname', '=', $_POST['user'])->limit(1)->execute();
		$user = $user[0];

		$twitter = new Twitter(Kohana::config('twitter.consumerToken'),  Kohana::config('twitter.consumerSecret'));
		$twitter->setOAuthToken($user['token']);
		$twitter->setOAuthTokenSecret($user['secret']);

		print_r($twitter->accountUpdateProfileImage(APPPATH . '../avatars/' . $user['image']));
	}

	public function action_horse()
	{
		
		die('You can\'t upload pics that way anymore! Try the new way!');

		if (empty($_FILES['pictur']))
		{
			die('forgot a pic');
		}
		if (isset($_POST['pass']) && $_POST['pass'] == 'horseDix99')
		{
			$twitter = new Twitter(Kohana::config('twitter.consumerToken'),  Kohana::config('twitter.consumerSecret'));

			if (isset($_POST['username']))
			{
				$user = DB::select()->from('users')->where('screenname', '=', $_POST['username'])->limit(1)->execute();
			}
			else
			{
				$user = DB::select()->from('users')->where('image', 'IS', DB::expr('NULL'))->limit(1)->execute();
			}

			$user = $user[0];
			$filename = time() . $_FILES['pictur']['name'];
			if ($user['id'] < 1)
			{
				copy($_FILES['pictur']['tmp_name'], APPPATH . '../avatars/' . $filename);
				DB::insert('waiting_images', array('image'))->values(array(APPPATH . '../avatars/' . $filename))->execute();
				die('out of people, storing in wait');
			}

			$twitter->setOAuthToken($user['token']);
			$twitter->setOAuthTokenSecret($user['secret']);

			copy($_FILES['pictur']['tmp_name'], APPPATH . '../avatars/' . $filename);

			DB::update('users')->set(array('image' => $filename))->where('id', '=', $user['id'])->execute();

			try
			{
				$credentials = $twitter->accountVerifyCredentials();
			}
			catch (Exception $e)
			{
				$credentials = array();
			}
			
			$imageFailed = FALSE;

			if (isset($credentials['screen_name']))
			{
				try
				{
					$imgUpdate = $twitter->accountUpdateProfileImage(APPPATH . '../avatars/' . $filename);
				}
				catch (Exception $e)
				{
					$imageFailed = TRUE;
					echo 'image set failed: ' . $e->getMessage() . "<br/>\r\n";
				}

				if ($imageFailed == FALSE)
				{
					echo "<!--", print_r(imgUpdate, true), "-->";
					$staUpdate = $twitter->statusesUpdate('check out my great avatar - I just @EightShit !!! http://eightshit.me #eightshit');
					echo "<!--", print_r(staUpdate, true), "-->";
				}

				try
				{
					// $twitter->friendshipsCreate(''); // same as above
				}
				catch (Exception $e)
				{

				}
				echo "Cool, image has been set for " + $user['screenname'] . "<br/>\r\n";
			}
			else
			{
				$imageFailed = TRUE;
				echo "Twitter Auth was disabled by user\r\n";
				DB::update('users')->set(array('active_creds' => 0))->where('id', '=', $user['id'])->execute();
			}

			if ($imageFailed == TRUE) {
				$tw2 = new Twitter(Kohana::config('twitter.consumerToken'),  Kohana::config('twitter.consumerSecret'));
				$tw2->setOAuthToken(Kohana::config('eightshit.bot.token'));
				$tw2->setOAuthTokenSecret(Kohana::config('eightshit.bot.secret'));
				$tw2->statusesUpdate("@{$user['screenname']} twitter auth failed, here's your image: http://eightshit.me/avatars/" . $filename . " - love you");
				echo "Twitter Auth Failed, Messaged the guy about it";
			}
		}
	}

	function action_reclaim()
	{
		$this->response->body(View::factory('layout', array('content' => View::factory('reclaim')))->render());
	}

	function action_profile()
	{
		if ($this->request->param('user') == NULL)
		{
			$this->response->status = 404;
		}
		else
		{
			try
			{
				$user = DB::select()->from('users')->where('screenname', '=', $this->request->param('user'))->where('image', 'IS NOT', DB::expr('null'))->execute();
				$user = $user[0];
			}
			catch (Exception $e)
			{
				$this->response->status = 404;
			}

			if (empty($user) || $user['id'] < 1)
			{
				$this->response->status = 404;
			}
			else
			{
				$this->response->body(View::factory('layout', array('content' => View::factory('profile', array('user' => $user))))->render());
			}
		}
	}

	public function action_dont_say_shit()
	{
		DB::update('users')
		->set(array('say_shit' => '0'))
		->where('screenname', '=', Session::instance()->get('twitter_screenname'))
		->execute();
	}

}

// End Welcome
