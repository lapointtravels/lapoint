<?php
global $KMC_MODULES, $kmc_module_objects, $kmc_sections;

foreach ($KMC_MODULES as $module) :
	$module->render_admin_templates();
endforeach;

global $module_controller;
$theme_modules = $module_controller->theme_modules;
if (sizeof($theme_modules) > 0) :

	// Include admin templates for theme components
	foreach ($theme_modules as $theme_module) :
		$theme_module = (object) $theme_module;
		if (isset($theme_module->admin_template)) :
			require_once($theme_module->admin_template);
		endif;
	endforeach;
	?>

	<script>
	var theme_modules_list = <?php echo json_encode($theme_modules); ?>;
	var theme_modules_map = {};
	for (var i=0; i<theme_modules_list.length; i++) {
		theme_modules_map[theme_modules_list[i].key] = theme_modules_list[i];
	};
	var THEME_MODULES = theme_modules_map;
	</script>
<?php else : ?>
	<script>
	var THEME_MODULES = {};
	</script>
<?php endif; ?>

<script>
window.kmc =  window.kmc || {};
window.kmc.logEnabled = localStorage.getItem('kmcLogEnabled') || false;
if (window.kmc.logEnabled) {
	console.log('***** KMC logging enabled *****');
}
window.kmc.settings = <?php echo json_encode($module_controller->_settings); ?>;
window.kmc.getSetting = function (arr, defaultSetting) {
	var _settings = window.kmc.settings;
	for (var i=0; i<arr.length; i++) {
		if (_settings[arr[i]]) {
			_settings = _settings[arr[i]];
		} else {
			return defaultSetting;
		}
	}
	return _settings;
}
window.kmc.printPxIfSet = function (val) {
	return (val || val === 0) ? ' (' + val + ' px)' : '';
}

window.kmc.selectOptions = {
	width: [
		['', 'Contained'],
		['extra-contained', 'Extra contained'],
		['full_width', 'Full width']
	]
}
</script>

<!-- Component Header -->
<script type="text/template" id="kmc-component-head-template">
	<span class="dashicons dashicons-sort sort-handle"></span>
	<span class="component-type">
		<%= label %>
		<% if (post.post_title) { %>
			<span class="title"><%= post.post_title.replace(/<br>/g, " ") %></span>
		<% } %>
	</span>
	<% if (edit) { %>
		<a href="#" class="dashicons dashicons-no active ball btn-return"></a>
		<a href="#" class="dashicons dashicons-networking ball component-info"></a>
		<a href="#" class="dashicons dashicons-trash ball remove"></a>
		<% if (show_settings) { %>
			<a href="#" class="dashicons dashicons-admin-generic ball edit-settings"></a>
		<% } %>
	<% } else { %>
		<a href="#" class="dashicons dashicons-edit ball edit"></a>
	<% } %>
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
			<span class="add-new-module">
				<i class="dashicons dashicons-plus"></i>
			</span>
		</div>
	</div>
</script>

<!-- Edit Section Dialog -->
<script type="text/template" id="kmc-edit-section-dialog-tab-1-template">
	<ul class="form-group form-list">
		<li>
			<label for="section-name"><?php _e("Name:", "kmc"); ?></label>
			<input type="input" id="section-name" class="form-control section-name" value="<%= settings.name || "" %>">
		</li>
		<li>
			<label for="section-background-color"><?php _e("Background color:", "kmc"); ?></label>
			<input type="input" id="section-background-color" class="jscolor form-control section-background-color" value="<%= settings.background_color || "" %>">
			<span class="dashicons dashicons-dismiss clear-color-icon" data-target="section-background-color"></span>
		</li>
		<li<% if (settings.background_image) print(" class='image-row'") %>>
			<label><?php _e("Background image:", "kmc"); ?></label>
			<% if (settings.background_image) { %>
				<img src="<%= settings.background_image.thumbnail %>" style="width: 80px; height: 80px;">
				<div class="pam">
					<a href="#" class="remove-image"><?php _e("Remove image", "kmc"); ?></a>
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
				<a href="#" class="select-image"><?php _e("Select image", "kmc"); ?></a>
			<% } %>
		</li>
		<li<% if (settings.background_video_ogv) print(" class='video-row'") %>>
			<label><?php _e("Background video (ogv):", "kmc"); ?></label>
			<% if (settings.background_video_ogv) { %>
				<span><%= settings.background_video_ogv.filename %></span>
				<div class="plm">
					<a href="#" class="remove-ogv-video"><?php _e("Remove video", "kmc"); ?></a>
				</div>
			<% } else { %>
				<a href="#" class="select-ogv-video"><?php _e("Select video", "kmc"); ?></a>
			<% } %>
		</li>
		<li<% if (settings.background_video_mp4) print(" class='video-row'") %>>
			<label><?php _e("Background video (mp4):", "kmc"); ?></label>
			<% if (settings.background_video_mp4) { %>
				<span><%= settings.background_video_mp4.filename %></span>
				<div class="plm">
					<a href="#" class="remove-mp4-video"><?php _e("Remove video", "kmc"); ?></a>
				</div>
			<% } else { %>
				<a href="#" class="select-mp4-video"><?php _e("Select video", "kmc"); ?></a>
			<% } %>
		</li>
	</ul>

	<button type="button" class="md-save button button-secondary btn-save-section-background"><?php _e("Save", "kmc"); ?></button>
</script>
<script type="text/template" id="kmc-edit-section-dialog-tab-2-template">
	<ul class="form-group form-list">
		<li>
			<label for="section-color-class"><?php _e("Text color", "kmc"); ?>:</label>
			<select class="section-color-class form-control">
				<option value="default"<% if (!settings.color_class || settings.color_class == "default") print(' selected="selected"'); %>>Default</option>
				<option value="dark"<% if (settings.color_class == "dark") print(' selected="selected"'); %>>Dark</option>
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

	<button type="button" class="md-save button button-secondary btn-save-section-style"><?php _e("Save", "kmc"); ?></button>
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
				<h3><?php _e("Edit section", "kmc"); ?></h3>
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
	<%
	var space = window.kmc.getSetting(['space', 'components'], {});
	var printSpace = window.kmc.printPxIfSet;
	%>
	<div class="md-modal" id="edit-component-dialog">
		<div class="md-content">
			<header>
				<h3><?php _e("Edit component", "kmc"); ?></h3>
				<a href="#" class="md-close"><span class="dashicons dashicons-no"></span></a>
			</header>
			<div class="md-body pam">

				<ul class="form-group form-list lbl-md">
					<li>
						<label for="width">Width:</label>
						<% if (fixedSettings.width) {
							var option = _.find(window.kmc.selectOptions.width, function (o) {
								return o[0] == fixedSettings.width;
							});
							%>
							<span class="fixed-setting">
								<% if (option) {
									print(option[1]);
								} else {
									print(fixedSettings.width);
								} %>
							</span>
						<% } else { %>
							<select class="width form-control">
								<%= kmc.helpers.render_options(window.kmc.selectOptions.width, settings.width) %>
							</select>
						<% } %>
					</li>
					<li>
						<label for="top-padding">Top padding:</label>
						<select class="top-padding form-control">
							<%
							print(kmc.helpers.render_options([
								["", "Default" + printSpace(space.default_padding_top)],
								["no", "None"],
								["xs", "Extra small" + printSpace(space.xs)],
								["sm", "Small" + printSpace(space.sm)],
								["md", "Medium" + printSpace(space.md)],
								["lg", "Large" + printSpace(space.lg)],
								["xl", "Extra large" + printSpace(space.xl)]
							], settings.top_padding)) %>
						</select>
					</li>
					<li>
						<label for="bottom-padding">Bottom padding:</label>
						<select class="bottom-padding form-control">
							<%= kmc.helpers.render_options([
								["", "Default" + printSpace(space.default_padding_bottom)],
								["no", "None"],
								["xs", "Extra small" + printSpace(space.xs)],
								["sm", "Small" + printSpace(space.sm)],
								["md", "Medium" + printSpace(space.md)],
								["lg", "Large" + printSpace(space.lg)],
								["xl", "Extra large" + printSpace(space.xl)]
							], settings.bottom_padding) %>
						</select>
					</li>
					<li>
						<label for="top-margin">Top margin:</label>
						<select class="top-margin form-control">
							<%= kmc.helpers.render_options([
								["", "Default" + printSpace(space.default_margin_top)],
								["no", "None"],
								["xs", "Extra small" + printSpace(space.xs)],
								["sm", "Small" + printSpace(space.sm)],
								["md", "Medium" + printSpace(space.md)],
								["lg", "Large" + printSpace(space.lg)],
								["xl", "Extra large" + printSpace(space.xl)]
							], settings.top_margin) %>
						</select>
					</li>
					<li>
						<label for="bottom-margin">Bottom margin:</label>
						<select class="bottom-margin form-control">
							<%= kmc.helpers.render_options([
								["", "Default" + printSpace(space.default_margin_bottom)],
								["no", "None"],
								["xs", "Extra small" + printSpace(space.xs)],
								["sm", "Small" + printSpace(space.sm)],
								["md", "Medium" + printSpace(space.md)],
								["lg", "Large" + printSpace(space.lg)],
								["xl", "Extra large" + printSpace(space.xl)]
							], settings.bottom_margin) %>
						</select>
					</li>

					<?php if (sizeof($module_controller->animation_types) > 0) : ?>
						<li>
							<label for="animation"><?php _e("Animation:", "kmc"); ?></label>
							<select class="animation form-control">
								<%= kmc.helpers.render_options([
									["", "None"],
									<?php foreach ($module_controller->animation_types as $animation) :
										echo "['" . $animation[0] . "', '" . $animation[1] . "'],";
									endforeach; ?>
								], settings.animation) %>
							</select>
						</li>
						<li>
							<label for="animation-delay"><?php _e("Animation delay:", "kmc"); ?></label>
							<input type="input" id="animation-delay" class="form-control animation-delay" value="<%= settings.animation_delay || 0 %>">
							<i>ms</i>
						</li>
					<?php endif; ?>
				</ul>

				<button type="button" class="md-save button button-secondary btn-save"><?php _e("Save", "kmc"); ?></button>
			</div>
		</div>
	</div>
	<div class="md-overlay"></div>
</script>



<!-- Component Info Dialog -->
<script type="text/template" id="kmc-component-info-dialog-template">
	<div class="md-modal" id="component-info-dialog">
		<div class="md-content">
			<header>
				<h3><?php _e("Component info", "kmc"); ?></h3>
				<a href="#" class="md-close"><span class="dashicons dashicons-no"></span></a>
			</header>
			<div class="md-body pam"></div>
		</div>
	</div>
	<div class="md-overlay"></div>
</script>
<script type="text/template" id="kmc-component-info-dialog-inner-template">
	<% if (component.get("shared")) { %>
		<div class="center">
			<h3><?php _e("This component is shared", "kmc"); ?></h3>
			<p><?php _e("It's possible to embed it on other pages, by using the \"Add existing\" button.", "kmc"); ?></p>
		</div>
	<% } else { %>
		<div class="center">
			<h3><?php _e("This component is not shared", "kmc"); ?></h3>
			<p><?php _e("If you want to embed it on other pages you need to share it first.", "kmc"); ?></p>
			<button class="button button-secondary mtm btn-share"><?php _e("Share component", "kmc"); ?></button>
		</div>
	<% } %>

	<div class="component-info hidden center mtl">
		<h3><?php _e("Instances", "kmc"); ?></h3>
		<p class="info" style="display: block; max-height: 190px; overflow-y: auto; overflow-x: hidden;"></p>

		<div class="break-free hidden">
			<p style="font-size: 10px;"><?php _e('If you make changes to the component on this page, the changes will also affect the components on the posts listed above.<br>
			You can "break free" the component which will remove the link between it and the instances on other pages, which means you can edit the component on this page without changing anything elsewhere. Just click the button below:', "kmc"); ?></p>
			<button class="break-free button button-secondary mts"><?php _e("Break free", "kmc"); ?></button>
		</div>
	</div>
</script>


<!-- New Component Dialog -->
<script type="text/template" id="kmc-new-dialog-template">
	<div class="md-modal" id="new-module-dialog">
		<div class="md-content">
			<header>
				<h3><?php _e("Add new content", "kmc"); ?></h3>
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
			<div class="md-body center">
			or <a href="#" class="add-existing-module"><?php _e("add existing content", "kmc"); ?></a>
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
				<h3><?php _e("Add existing content", "kmc"); ?></h3>
				<a href="#" class="md-close"><span class="dashicons dashicons-no"></span></a>
			</header>
			<div class="md-body">
				<div>
					<p>
						<?php _e("You can add already existing content to this page by using the form below. The content will be added as a reference, which means that any changes you make on any page where the content is included will take effect in all locations.", "kmc"); ?>
					</p>
					<ul class="form-group form-list">
						<li>
							<label for="width"><?php _e("Select existing content type:", "kmc"); ?></label>
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
					<p style="color: red;" class="no-found center pas hidden">
						<?php _e("No shared components for the selected type were found. If you wan't to add an existing component from another page make sure to share it first."); ?>
					</p>
					<div class="result-list hidden">
						<ul class="form-group form-list">
							<li>
								<label for="width"><?php _e("Select content:", "kmc"); ?></label>
								<select class="select-module">
								</select>
							</li>
						</ul>
						<button type="button" class="btn-add-module button mtm"><?php _e("Add to page", "kmc"); ?></button>
					</div>
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
				<h3><?php _e("Import content", "kmc"); ?></h3>
				<a href="#" class="md-close"><span class="dashicons dashicons-no"></span></a>
			</header>
			<div class="md-body">
				<p>
					<?php _e("Copy content from another page created with the Page Composer tool.<br>
					Select the page to copy below:", "kmc"); ?>
				</p>

				<div>
					<ul class="form-group form-list lbl-md">
						<li>
							<label for="width"><?php _e("Content type:", "kmc"); ?></label>
							<select class="select-type form-control">
								<option value="-" selected="selected"><?php _e("Select type", "kmc"); ?></option>
								<?php foreach ($module_controller->post_types as $post_type) : ?>
									<?php if ($post_type) : ?>
										<option value="<?php echo $post_type->name; ?>"><?php echo $post_type->labels->name; ?></option>
									<?php endif; ?>
								<?php endforeach; ?>
							</select>
						</li>
					</ul>
				</div>
				<div class="select-post-container hidden">
					<ul class="form-group form-list lbl-md">
						<li>
							<label for="width"><?php _e("Select content:", "kmc"); ?></label>
							<select class="select-post">
							</select>
						</li>
					</ul>
					<button type="button" class="btn-select-post button mtm"><?php _e("Copy", "kmc"); ?></button>
				</div>
			</div>
		</div>
	</div>
	<div class="md-overlay"></div>
</script>
<script>

window.KMC_MODULES_MODELS = {};

<?php if (defined('ICL_LANGUAGE_CODE')) : ?>
	var wpml_lang_code = '<?php echo ICL_LANGUAGE_CODE ?>';
<?php endif; ?>

var kmc_modules_classes = <?php echo json_encode($kmc_module_objects); ?>,
	kmc_sections = <?php echo json_encode($kmc_sections); ?>;
</script>


<div class="clearfix">
	<input type="hidden" id="kmc_page_components" name="kmc_page_components" value="">
	<div class="section-container"></div>
	<div class="mtxl mbm center">
		<button type="button" class="button button-secondary add-new-section"><?php _e("Add section", "kmc"); ?></button>
		<button type="button" class="button button-secondary btn-import"><?php _e("Import content", "kmc"); ?></button>
	</div>
</div>