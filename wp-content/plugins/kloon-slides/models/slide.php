<?php

class Kloon_Slides_Slide extends Kloon_Slides_Abstract_Model {

	public function __construct ($post) {
		parent::__construct($post);


		$data = json_decode($post->post_content);
		$this->data = $data;

		$this->type = $this->data->type;
		$this->presentation = $this->data->presentation;

		/*
		echo "**********";
		var_dump($data);
		echo "**********";

		$meta = get_post_meta($this->id);

		$this->type = $meta["type"][0];
		$this->presentation = $meta["presentation"][0];
		$this->image_data = json_decode($meta["image_data"][0]);
		*/

		// Copy all presentation data into a seperate object
		$presentation_object = false;
		$presentation_data = array();
		if ($this->data->presentation) {
			$presentation_object = $this->get_presentation_object();
			if ($presentation_object) {
				foreach ($presentation_object->fields as $field) {
					if ($field["type"] == "link") {
						$presentation_data[$field["key"] . "_label"] = $this->data->{$field["key"] . "_label"};
						$presentation_data[$field["key"] . "_url"] = $this->data->{$field["key"] . "_url"};
					} else {
						$presentation_data[$field["key"]] = $this->data->{$field["key"]};
					}
				}
			}
		}
		$this->presentation_data = $presentation_data;

		if ($this->type == "video") {
			$this->is_video = true;
			$this->is_image = false;
			$this->is_youtube = ($this->data->video_type == "youtube");
			$this->is_vimeo = ($this->data->video_type == "vimeo");

			if ($presentation_object && $presentation_object->settings) {
				$this->has_bgr_video = $presentation_object->settings["has_bgr_video"];
			} else {
				$this->has_bgr_video = false;
			}
		} else {
			$this->is_video = false;
			$this->is_image = true;
			$this->is_youtube = false;
			$this->is_vimeo = false;
		}
	}

	public function reload () {
		return new Kloon_Slides_Slide(get_post($this->id));
	}

	public function get_presentation_object ($presentation_id = false) {
		if (!$presentation_id) {
			$presentation_id = $this->presentation;
		}

		if ($presentation_id) {
			global $kloon_slides;
			$settings = $kloon_slides->get_settings();
			if ($this->type == "video") {
				$presentations = $settings["video_presentations"];
			} else {
				$presentations = $settings["image_presentations"];
			}

			if ($presentations && $presentations[$presentation_id]) {
				return (object) $presentations[$presentation_id];
			}
		}

		return false;
	}

	public function get_presentation_object_field_map () {
		$presentation_object = $this->get_presentation_object();

		$map = array();
		if ($presentation_object) {
			foreach ($presentation_object->fields as $field) {
				$map[$field["key"]] = $field;
			}
		}

		return $map;
	}

	public function set_data ($data) {
		wp_update_post(array(
			'ID' => $this->id,
			'post_content' => json_encode($data, JSON_HEX_APOS | JSON_HEX_QUOT | JSON_HEX_AMP | JSON_UNESCAPED_UNICODE)
		));
	}

	public function update () {
		$data = $this->data;
		$data->presentation = $_POST["presentation"];

		$presentation_object = $this->get_presentation_object($_POST["presentation"]);
		foreach ($presentation_object->fields as $field) {
			if ($field["type"] == "link") {

				$val = $_POST[$field["key"] . "_label"];
				if ($val) {
					$data->{$field["key"] . "_label"} = $val;
				}

				$val = $_POST[$field["key"] . "_url"];
				if ($val) {
					$data->{$field["key"] . "_url"} = $val;
				}

			} else {
				$val = $_POST[$field["key"]];
				if ($val) {
					$data->{$field["key"]} = $val;
				}
			}
		}


		if ($this->type == "image") {

		} else if ($this->type == "video") {
			$data->video_id = $_POST["video_id"];
			$data->width = $_POST["width"];
			$data->height = $_POST["height"];
			$data->autoplay = ($_POST["autoplay"] == "true");
			$data->keep_proportions = ($_POST["keep_proportions"] == "true");

			if ($presentation_object->settings && $presentation_object->settings["has_bgr_video"]) {
				$data->background_video_ogv = $_POST["background_video_ogv"];
				$data->background_video_mp4 = $_POST["background_video_mp4"];
			}
		}


		$this->set_data($data);

		/*
		$this->set_data(array(
			"presentation" => $_POST["presentation"]
		));

		$presentation_object = $this->get_presentation_object();
		$presentation_data = array();
		foreach ($presentation_object["fields"] as $field) {
			$val = $_POST[$field["key"]];
			if ($val) {
				update_post_meta($this->id, $field["key"], $val);
				$presentation_data[$field["key"]] = $val;
			}
		}
		$this->presentation_data = $presentation_data;*/
	}

	public function get_image_url ($size = "lg") {
		if (!$this->is_image) {
			return;
		}

		global $kloon_slides;
		$settings = $kloon_slides->get_settings();
		$image_data = $this->data->image_data;

		// Return thumbnail if specified
		if (($size == "thumb" || $size == "thumbnail") && isset($this->image_data->thumbnail)) {
			return $image_data->thumbnail;
		}

		if (
			isset($image_data->sizes->{$settings["media_library_sizes"][$size]["name"]}) &&
			$image_data->sizes->{$settings["media_library_sizes"][$size]["name"]}->url &&
			file_exists($image_data->sizes->{$settings["media_library_sizes"][$size]["name"]}->url)
		) {
			return $image_data->sizes->{$settings["media_library_sizes"][$size]["name"]}->url;
		} else {
			return $image_data->sizes->{$settings["media_library_sizes"]["md"]["name"]}->url;
		}
	}

	public function get_image ($size = "lg") {
		if (!$this->is_image) {
			return;
		}

		global $kloon_slides;
		$settings = $kloon_slides->get_settings();
		$image_data = $this->data->image_data;

		$image_data_size = false;
		if (isset($image_data->sizes->{$settings["media_library_sizes"][$size]["name"]})) {
			$image_data_size = $image_data->sizes->{$settings["media_library_sizes"][$size]["name"]};
		}

		if ($image_data_size && $image_data_size->url != ""
			// && file_exists($image_data->sizes->{$settings["media_library_sizes"][$size]["name"]}->url)
		) {
			return $image_data_size;
		} else {
			return $image_data->sizes->{$settings["media_library_sizes"]["md"]["name"]};
		}
	}

	public function get_presentation_display_data () {
		// Returns an object with data for frontend
		$classes = "";
		$presentation_output = "";
		$presentation_object = $this->get_presentation_object();

		if ($this->data->presentation && $presentation_object) {
			foreach ($presentation_object->fields as $field) {
				$key = $field["key"];
				$field_classes = isset($field["classes"]) ? $field["classes"] : "";

				if ($field["append_class"]) {
					// If the append_class attribute is set on the field, it should not
					// display anything, but instead add a class to the slide element
					$classes .= " " . $key . "-" . $this->data->{$key};
				} else {
					$tag = ($field["tag"]) ? $field["tag"] : "div";
					if ($field["type"] == "link") {
						if ($this->data->{$key ."_label"} && $this->data->{$key ."_url"}) {
							$presentation_output .= "<div class='" . $key . "-container'><a href='" . $this->data->{$key ."_url"} . "' class='presentation-" . $key . " " . $field_classes . "'>" . $this->data->{$key ."_label"} . "</a></div>";
						}
					} else {
						if ($this->data->{$key}) {
							$presentation_output .= "<" . $tag . " class='presentation-" . $key . " " . $field_classes . "'>" . $this->data->{$key} . "</" . $tag . ">";
						}
					}
				}
			}
		}

		return (object) array(
			"classes" => $classes,
			"presentation_output" => $presentation_output,
		);
	}

}
