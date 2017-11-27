
	<ul class="kloonslides-ul-settings">
		<li class="clearfix">
			<label>Video type:</label>
			<select id="video-type">
				<option value="youtube">Youtube</option>
				<?php /*<option value="vimeo">Vimeo</option>*/ ?>
			</select>
		</li>
		<li class="clearfix">
			<label>Video ID:</label>
			<input type="text" id="video-id" value="" placeholder="Ange youtube id">
		</li>
		<li class="clearfix">
			<label>Bredd:</label>
			<input type="text" id="video-width" value="640">
		</li>
		<li class="clearfix">
			<label>Höjd:</label>
			<input type="text" id="video-height" value="320">
		</li>
	</ul>

	<div class="clearfix" style="margin-top: 15px;">
		<button type="button" class="button-primary btn-add-video">Add slide</button>
		<span id="add-video-message" style="display:none;">Saving, please wait..</span>
	</div>