<?php
global $KMC_MODULES, $kmc_module_objects, $kmc_sections;

foreach ($KMC_MODULES as $module) :
	$module->render_admin_templates();
endforeach;

?>

<!-- Component Header -->
<script type="text/template" id="kmc-component-head-template">
	<header>
		<span class="dashicons dashicons-sort sort-handle"></span>
		<span class="component-type"><%= label %></span>
		<% if (post.post_title) { %>
			<span class="title"><%= post.post_title.replace(/<br>/g, " ") %></span>
		<% } %>
		<% if (edit) { %>
			<a href="#" class="dashicons dashicons-no active ball btn-return"></a>
			<a href="#" class="dashicons dashicons-trash ball remove"></a>
			<% if (show_settings) { %>
				<a href="#" class="dashicons dashicons-admin-generic ball edit-settings"></a>
			<% } %>
		<% } else { %>
			<a href="#" class="dashicons dashicons-edit ball edit"></a>
		<% } %>
	</header>
</script>

<!-- Module Section -->
<script type="text/template" id="kmc-module-section-template">
	<div class="module-section">
		<span class="dashicons dashicons-sort sort-handle ball sort-section"></span>
		<div class="section-menu">
			<a href="#" class="dashicons dashicons-edit ball open-section-menu"></a>
			<ul class="menu">
				<li><a href="#" class="remove-section dashicons dashicons-trash ball"></a></li>
				<li><a href="#" class="edit-section dashicons dashicons-admin-generic ball"></a></li>
			</ul>
		</div>
		<div class="modules-container"></div>
		<div class="btn-container">
			<button type="button" class="button button-secondary add-new-module">Add new</button>
			<button type="button" class="button button-secondary add-existing-module">Add existing</button>
		</div>
	</div>
</script>

<!-- Edit Section Dialog -->
<script type="text/template" id="kmc-edit-section-dialog-tab-1-template">
	<ul class="form-group form-list">
		<li>
			<label for="section-name">Name:</label>
			<input type="input" id="section-name" class="form-control section-name" value="<%= settings.name || "" %>">
		</li>
		<li>
			<label for="section-background-color">Background color:</label>
			<input type="input" id="section-background-color" class="jscolor form-control section-background-color" value="<%= settings.background_color || "" %>">
			<span class="dashicons dashicons-dismiss clear-color-icon" data-target="section-background-color"></span>
		</li>
		<li<% if (settings.background_image) print(" class='image-row'") %>>
			<label>Background image:</label>
			<% if (settings.background_image) { %>
				<img src="<%= settings.background_image.thumbnail %>" style="width: 80px; height: 80px;">
				<div class="pam">
					<a href="#" class="remove-image">Remove image</a>
					<div>
						<label>Size: </label>
						<select class="background-image-size">
							<option value="cover"<% if (settings.background_image.size == "cover") print(" selected='selected'") %>>Cover</option>
							<option value="contain"<% if (settings.background_image.size == "contain") print(" selected='selected'") %>>Contain</option>
							<option value="repeat"<% if (settings.background_image.size == "repeat") print(" selected='selected'") %>>Repeated</option>
						</select>
					</div>
				</div>
			<% } else { %>
				<a href="#" class="select-image">Select image</a>
			<% } %>
		</li>
		<li<% if (settings.background_video_ogv) print(" class='video-row'") %>>
			<label>Background video (ogv):</label>
			<% if (settings.background_video_ogv) { %>
				<span><%= settings.background_video_ogv.filename %></span>
				<div class="plm">
					<a href="#" class="remove-ogv-video">Remove video</a>
				</div>
			<% } else { %>
				<a href="#" class="select-ogv-video">Select video</a>
			<% } %>
		</li>
		<li<% if (settings.background_video_mp4) print(" class='video-row'") %>>
			<label>Background video (mp4):</label>
			<% if (settings.background_video_mp4) { %>
				<span><%= settings.background_video_mp4.filename %></span>
				<div class="plm">
					<a href="#" class="remove-mp4-video">Remove video</a>
				</div>
			<% } else { %>
				<a href="#" class="select-mp4-video">Select video</a>
			<% } %>
		</li>
	</ul>

	<button type="button" class="md-save button button-secondary btn-save-section-background">Save</button>
</script>
<script type="text/template" id="kmc-edit-section-dialog-tab-2-template">
	<ul class="form-group form-list">
		<li>
			<label for="section-color-class">Text color:</label>
			<select class="section-color-class form-control">
				<option value="dark"<% if (!settings.color_class || settings.color_class == "dark") print(' selected="selected"'); %>>Dark</option>
				<option value="light"<% if (settings.color_class == "light") print(' selected="selected"'); %>>Light</option>
				<option value="custom"<% if (settings.color_class == "custom") print(' selected="selected"'); %>>Custom</option>
			</select>
			<div class="color-container hidden">
				<input id="section-color" type="input" class="jscolor form-control section-color pull-left" value="<%= settings.color %>">
				<span class="dashicons dashicons-dismiss clear-color-icon" data-target="section-color pull-left" style="top: 8px;"></span>
			</div>
		</li>
		<li>
			<label for="top-padding">Top padding:</label>
			<select class="top-padding form-control">
				<%= kmc.helpers.render_options([
					["", "None"],
					["sm", "Small"],
					["md", "Medium"],
					["lg", "Large"],
					["xl", "Extra large"]
				], settings.top_padding) %>
			</select>
		</li>
		<li>
			<label for="bottom-padding">Bottom padding:</label>
			<select class="bottom-padding form-control">
				<%= kmc.helpers.render_options([
					["", "None"],
					["sm", "Small"],
					["md", "Medium"],
					["lg", "Large"],
					["xl", "Extra large"]
				], settings.bottom_padding) %>
			</select>
		</li>
		<li>
			<label for="top-margin">Top margin:</label>
			<select class="top-margin form-control">
				<%= kmc.helpers.render_options([
					["", "None"],
					["sm", "Small"],
					["md", "Medium"],
					["lg", "Large"],
					["xl", "Extra large"]
				], settings.top_margin) %>
			</select>
		</li>
		<li>
			<label for="bottom-margin">Bottom margin:</label>
			<select class="bottom-margin form-control">
				<%= kmc.helpers.render_options([
					["", "None"],
					["sm", "Small"],
					["md", "Medium"],
					["lg", "Large"],
					["xl", "Extra large"]
				], settings.bottom_margin) %>
			</select>
		</li>
	</ul>

	<button type="button" class="md-save button button-secondary btn-save-section-style">Save</button>
</script>
<script type="text/template" id="kmc-edit-section-dialog-tab-3-template">
	<ul class="form-group form-list">
		<li>
			<label for="section-extra-class">Custom class:</label>
			<input type="text" class="form-control section-extra-class" value="<%= settings.extra_class %>">
		</li>
		<li>
			<label for="section-css">Custom CSS:</label>
			<textarea class="form-control section-css"><%= settings.css %></textarea>
		</li>
	</ul>

	<button type="button" class="md-save button button-secondary btn-save-section-css">Save</button>
</script>
</script>
<script type="text/template" id="kmc-edit-section-dialog-template">
	<div class="md-modal" id="edit-section-dialog">
		<div class="md-content">
			<header>
				<h3>Edit section</h3>
				<a href="#" class="md-close"><span class="dashicons dashicons-no"></span></a>
			</header>
			<div class="md-body">

				<div class="tabs">
					<ul class="tabs-head clearfix">
						<li><a href="#tabs-1">Misc</a></li>
						<li><a href="#tabs-2">Style</a></li>
						<li><a href="#tabs-3">CSS</a></li>
					</ul>
					<div id="tabs-1"></div>
					<div id="tabs-2"></div>
					<div id="tabs-3"></div>
				</div>

			</div>
		</div>
	</div>
	<div class="md-overlay"></div>
</script>


<!-- Edit Component Dialog -->
<script type="text/template" id="kmc-edit-component-dialog-template">
	<div class="md-modal" id="edit-component-dialog">
		<div class="md-content">
			<header>
				<h3>Edit component</h3>
				<a href="#" class="md-close"><span class="dashicons dashicons-no"></span></a>
			</header>
			<div class="md-body pam">

				<ul class="form-group form-list lbl-md">
					<li>
						<label for="width">Width:</label>
						<select class="width form-control">
							<option value=""<% if (settings.width != "full_width") print(' selected="selected"'); %>>Contained</option>
							<option value="full_width"<% if (settings.width == "full_width") print(' selected="selected"'); %>>Full width</option>
						</select>
					</li>
					<li>
						<label for="top-padding">Top padding:</label>
						<select class="top-padding form-control">
							<%= kmc.helpers.render_options([
								["", "Default"],
								["no", "None"],
								["sm", "Small"],
								["md", "Medium"],
								["lg", "Large"],
								["xl", "Extra large"]
							], settings.top_padding) %>
						</select>
					</li>
					<li>
						<label for="bottom-padding">Bottom padding:</label>
						<select class="bottom-padding form-control">
							<%= kmc.helpers.render_options([
								["", "Default"],
								["no", "None"],
								["sm", "Small"],
								["md", "Medium"],
								["lg", "Large"],
								["xl", "Extra large"]
							], settings.bottom_padding) %>
						</select>
					</li>
					<li>
						<label for="top-margin">Top margin:</label>
						<select class="top-margin form-control">
							<%= kmc.helpers.render_options([
								["", "Default"],
								["no", "None"],
								["sm", "Small"],
								["md", "Medium"],
								["lg", "Large"],
								["xl", "Extra large"]
							], settings.top_margin) %>
						</select>
					</li>
					<li>
						<label for="bottom-margin">Bottom margin:</label>
						<select class="bottom-margin form-control">
							<%= kmc.helpers.render_options([
								["", "Default"],
								["no", "None"],
								["sm", "Small"],
								["md", "Medium"],
								["lg", "Large"],
								["xl", "Extra large"]
							], settings.bottom_margin) %>
						</select>
					</li>
				</ul>

				<button type="button" class="md-save button button-secondary btn-save">Save</button>


				<div class="component-info hidden mtm">
					<span class="info" style="display: block; max-height: 190px; overflow-y: auto; overflow-x: hidden;"></span>
					<button class="break-free button button-secondary mtm">Break free</button>
				</div>

			</div>
		</div>
	</div>
	<div class="md-overlay"></div>
</script>


<!-- New Component Dialog -->
<script type="text/template" id="kmc-new-dialog-template">
	<div class="md-modal" id="new-module-dialog">
		<div class="md-content">
			<header>
				<h3>Add new content</h3>
				<a href="#" class="md-close"><span class="dashicons dashicons-no"></span></a>
			</header>
			<div class="md-body clearfix">
				<?php

				global $module_controller;
				$category_data = $module_controller->get_category_info();
				// echo var_dump($category_data);

				$categories = array();
				foreach ($KMC_MODULES as $module) :
					if ($module->create_new) :
						if (!isset($categories[$module->category])) :
							$categories[$module->category] = array(
								"type" => $module->category,
								"modules" => array()
							);
						endif;
						$categories[$module->category]["modules"][] = $module;
					endif;
				endforeach;
				foreach ($categories as $category) :
					$cat_data = $category_data[$category["type"]]; ?>

					<div class="col">
						<img src="<?php echo $cat_data->icon; ?>" style="width: 50px; height: 50px;">
						<h4><?php echo $cat_data->title; ?></h4>

						<?php foreach ($category["modules"] as $module) : ?>
							<a href="#" class="add-content button" <?php if ($module->sub_support) echo "data-sub-support='true'"; ?> data-type="<?php echo $module->type; ?>"><?php echo $module->get_new_module_icon(); ?></a>
							<?php
						endforeach;
						?>
					</div>
					<?php
				endforeach;
				?>
			</div>
		</div>
	</div>
	<div class="md-overlay"></div>
</script>

<!-- Existing Module Dialog -->
<script type="text/template" id="kmc-existing-dialog-template">
	<div class="md-modal" id="new-module-dialog">
		<div class="md-content">
			<header>
				<h3>Add existing content</h3>
				<a href="#" class="md-close"><span class="dashicons dashicons-no"></span></a>
			</header>
			<div class="md-body">
				<div>
					<p>
						You can add already existing content to this page by using the form below. The content will be added as a reference, which means that any changes you make on any page where the content is included will take effect in all locations.
					</p>
					<ul class="form-group form-list">
						<li>
							<label for="width">Select existing content type:</label>
							<select class="select-type form-control mlm">
								<option value="-" selected="selected">Select type</option>
								<?php
								foreach ($kmc_module_objects as $module) :
									if ($module->fetch_existing) : ?>
										<option value="<?php echo $module->type; ?>"><?php echo $module->name; ?></option>
										<?php
									endif;
								endforeach;
								?>
							</select>
						</li>
					</ul>
				</div>
				<div class="select-module-container hidden">
					<ul class="form-group form-list">
						<li>
							<label for="width">Select content:</label>
							<select class="select-module">
							</select>
						</li>
					</ul>
					<button type="button" class="btn-add-module button mtm">Add to page</button>
				</div>
			</div>
		</div>
	</div>
	<div class="md-overlay"></div>
</script>

<!-- Import Content Dialog -->
<script type="text/template" id="kmc-import-content-template">
	<div class="md-modal" id="import-content-dialog">
		<div class="md-content">
			<header>
				<h3>Import content</h3>
				<a href="#" class="md-close"><span class="dashicons dashicons-no"></span></a>
			</header>
			<div class="md-body">
				<p>
					Copy content from another page created with the Page Composer tool.<br>
					Select the page to copy below:
				</p>

				<div>
					<ul class="form-group form-list lbl-md">
						<li>
							<label for="width">Content type:</label>
							<select class="select-type form-control">
								<option value="-" selected="selected">Select type</option>
								<?php foreach ($module_controller->post_types as $post_type) : ?>
									<option value="<?php echo $post_type->name; ?>"><?php echo $post_type->labels->name; ?></option>
								<?php endforeach; ?>
							</select>
						</li>
					</ul>
				</div>
				<div class="select-post-container hidden">
					<ul class="form-group form-list lbl-md">
						<li>
							<label for="width">Select content:</label>
							<select class="select-post">
							</select>
						</li>
					</ul>
					<button type="button" class="btn-select-post button mtm">Copy</button>
				</div>
			</div>
		</div>
	</div>
	<div class="md-overlay"></div>
</script>
<script>

window.KMC_MODULES_MODELS = {};

var wpml_lang_code = '<?php echo ICL_LANGUAGE_CODE ?>';

var kmc_modules_classes = <?php echo json_encode($kmc_module_objects); ?>,
	kmc_sections = <?php echo json_encode($kmc_sections); ?>;
</script>


<div class="clearfix">
	<input type="hidden" id="kmc_page_components" name="kmc_page_components" value="">
	<div class="section-container"></div>
	<div class="mtxl mbm center">
		<button type="button" class="button button-secondary add-new-section">Add section</button>
		<button type="button" class="button button-secondary btn-import">Import content</button>
	</div>
</div>