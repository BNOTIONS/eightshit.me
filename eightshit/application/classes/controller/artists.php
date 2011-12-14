<?php defined('SYSPATH') or die('No direct access allowed.');

class Controller_Artists extends Controller {

	public function action_submit()
	{		
		$layout = View::factory('layout');
		$layout->content = View::factory('artists/submit');
		$layout->content->user = Session::instance()->get('twitter_screenname');
		$layout->content->authenticated = !empty($layout->content->user);

		if(isset($_FILES['picture']))// && isset($_FILES['name']))
		{
			$msg = $this->_storeImage($_FILES['picture']);

			if($msg === TRUE)
			{
				$layout->content->message = 'Yes!!! You did it!! Our experts will take a look and assign it to a new friend';
			}
			else
			{
				$layout->content->error = $msg;
			}
		}

		$this->response->body($layout->render());

	}

	public function action_draw()
	{
		
		$layout = View::factory('layout');
		$layout->content = View::factory('artists/draw');

		try
		{
			$user = DB::select()->from('users')->where('screenname', '=', Session::instance()->get('twitter_screenname'))->execute();
			$user = $user[0];
		}
		catch(Exception $e)
		{
			$user = NULL;
		}

		if(is_array($user))
		{
			$layout->content->userid = $user['userid'];
		}
		else
		{
			$layout->content->userid = FALSE;
		}

		$this->response->body($layout->render());

	}

	protected function _storeImage($picture)
	{

		if(Session::instance()->get('twitter_screenname') === NULL)
		{
			// No session value - need to authenticate
			return "Gotta authenticate first!!! TWITTER";
		}
		else
		{
			$user = DB::select()->from('users')->where('screenname', '=', Session::instance()->get('twitter_screenname'))->limit(1)->execute()->as_array();
			if(sizeof($user) == 0)
			{
				// No user!!!
				return "You are not real";
			}
			else
			{
				$user = $user[0];

				$filename = time().$picture['name'];

				try
				{
					Image::factory($picture['tmp_name'])->resize(128, 128)->save(APPPATH.DIRECTORY_SEPARATOR.'incoming/'.$filename);
				}
				catch(Kohana_Exception $e)
				{
					return "Image Busted or something";
				}

				DB::insert('pending_images', array('user_id', 'image'))->values(array($user['id'], $filename))->execute();

			}

		}

		return TRUE;
	}

}
