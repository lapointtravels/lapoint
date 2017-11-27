<?php

class Kloon_Slide {

	public function __construct ($data) {
		//$this->data = $data;
		$this->autoplay = $data->autoplay;
		$this->created = $data->created;
		$this->dirname = $data->dirname;
		$this->filename = $data->filename;
		$this->height = $data->height;
		$this->id = $data->id;
		$this->keep_proportions = $data->keep_proportions;
		$this->link = $data->link;
		$this->mime = $data->mime;
		$this->position = $data->position;
		$this->slide_show_id = $data->slide_show_id;
		$this->slide_type = $data->slide_type;
		$this->text1 = $data->text1;
		$this->text2 = $data->text2;
		$this->text3 = $data->text3;
		$this->title = $data->title;
		$this->type = $data->type;
		$this->updated = $data->updated;
		$this->url = $data->url;
		$this->vertical_align = $data->vertical_align;
		$this->video_id = $data->video_id;
		$this->video_type = $data->video_type;
		$this->width = $data->width;
		$this->image_data = json_decode($data->image_data);
	}

	public function is_youtube () {
		return $this->video_type === "youtube";
	}

	public function is_vimeo () {
		return $this->video_type === "vimeo";
	}

	public function get_image_url ($size = False) {
		global $kloon_image_slider;
		$settings = $kloon_image_slider->get_settings();

		if (isset($this->image_data) && $this->image_data) {
			$image_data = $this->image_data;
			$sizes = array("lg", "md", "sm");

			if (!$size) {
				$size = "lg";
			}

			if (isset($image_data->sizes->{$settings["media_library_sizes"][$size]})) {
				return $image_data->sizes->{$settings["media_library_sizes"][$size]}->url;
			} else {
				return $image_data->sizes->{$settings["media_library_sizes"]["md"]}->url;
			}

			/*for ($i=0; $i<3; $i++) {
				//$check_size = $sizes[$i]
				//if ()
			}*/
		} else {
			$root = $settings["upload_dir_url"];
			if ($settings["use_seperate_upload_folders"]) {
				$root .= $this->slide_show_id ."/";
			}

			if ($size) {
				return $root . $this->filename . '-' . $size . '.' . $this->type;
			} else {
				return $root . $this->filename . '.' . $this->type;
			}
		}
	}

	public function get_image_path ($size = False) {
		if ($size) {
			return $this->dirname .'/'. $this->filename .'-' . $size . '.'. $this->type;
		} else {
			return $this->dirname .'/'. $this->filename .'.'. $this->type;
		}
	}

	public function delete_images ($keepOriginal = True) {
		if (!$keepOriginal) {
			delete_file($this->get_image_path());
			delete_file($this->get_image_path('thumb'));
		}
		delete_file($this->get_image_path('lg'));
		delete_file($this->get_image_path('md'));
		delete_file($this->get_image_path('sm'));
		delete_file($this->get_image_path('xs'));
	}

	public function regenerate_images ($size) {
		global $kloon_image_slider;
		$settings = $kloon_image_slider->get_settings();
		$sizes = $settings["sizes"];

		// First delete all current images except the original
		$this->delete_images(True);
		$original = $this->get_image_path();

		if ($size === "fixed") {
			image_resize($original, $sizes["lg"][0], $sizes["lg"][1], true, "lg");
			image_resize($original, $sizes["md"][0], $sizes["md"][1], true, "md");
			image_resize($original, $sizes["sm"][0], $sizes["sm"][1], true, "sm");
			image_resize($original, $sizes["xs"][0], $sizes["xs"][1], true, "xs");
		} else {
			image_resize($original, $sizes["lg"][0], 99999, false, "lg");
			image_resize($original, $sizes["md"][0], 99999, false, "md");
			image_resize($original, $sizes["sm"][0], 99999, false, "sm");
			image_resize($original, $sizes["xs"][0], 99999, false, "xs");
		}
	}

}
