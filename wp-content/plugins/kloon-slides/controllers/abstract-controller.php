<?php

class Kloon_Slides_Abstract_Controller {

	public function __construct () {
	}

	public function get ($id) {
		$post = get_post($id);
		return new $this->post_object($post);
	}

	public function get_all () {
		$posts = get_posts(array(
			'post_type' => $this->post_type,
			'posts_per_page' => -1,
			'orderby' => 'title',
			'order' => 'ASC'
		));

		if ($posts && sizeof($posts) > 0) {
			return array_map(function ($post) {
				return new $this->post_object($post);
	  		}, $posts);
		} else {
			return false;
		}
	}

}
