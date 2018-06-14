<div class="row">
	<div class="col-xs-4">
		Breite des Blocks (nur Detailansicht):
	</div>
	<div class="col-xs-8">
		<select name="REX_INPUT_VALUE[20]" >
		<?php
		$values = [12=>"12 von 12 Spalten (ganze Breite)", 8=>"8 von 12 Spalten", 6=>"6 von 12 Spalten", 4=>"4 von 12 Spalten", 3=>"3 von 12 Spalten"];
		foreach($values as $key => $value) {
			echo '<option value="'. $key .'" ';
	
			if ("REX_VALUE[20]" == $key) {
				echo 'selected="selected" ';
			}
			echo '>'. $value .'</option>';
		}
		?>
		</select>
	</div>
</div>
<div class="row">
	<div class="col-xs-4">
		Offset (Seitenabstand) auf größeren Geräten (nur Detailansicht):
	</div>
	<div class="col-xs-8">
		<select name="REX_INPUT_VALUE[17]" >
		<?php
		$values = array(0=>"Kein Offset", 1=>"Offset");
		foreach($values as $key => $value) {
			echo '<option value="'. $key .'" ';
	
			if ("REX_VALUE[17]" == $key) {
				echo 'selected="selected" ';
			}
			echo '>'. $value .'</option>';
		}
		?>
		</select>
	</div>
</div>
<div class="row">
	<div class="col-xs-12">&nbsp;</div>
</div>
<div class="row">
	<div class="col-xs-4">
		Maximale Anzahl angezeigter Referenzen
	</div>
	<div class="col-xs-8">
		<input type="number" size="5" name="REX_INPUT_VALUE[2]" value="REX_VALUE[2]" />
	</div>
</div>
<div class="row">
	<div class="col-xs-12">&nbsp;</div>
</div>
<div class="row">
	<div class="col-xs-12 col-md-3">
		Text oberhalb der Referenzliste:
	</div>
	<div class="col-xs-12 col-md-9">
		<?php
			$wysiwyg_class = ' ';
			if(rex_config::get('d2u_helper', 'editor') == 'tinymce4' && rex_addon::get('tinymce4')->isAvailable()) {
				$wysiwyg_class .= 'tinyMCEEditor';
			}
			else if(rex_config::get('d2u_helper', 'editor') == 'redactor2' && rex_addon::get('redactor2')->isAvailable()) {
				$wysiwyg_class .= 'redactorEditor2-full';
			}
			else if(rex_config::get('d2u_helper', 'editor') == 'ckeditor' && rex_addon::get('ckeditor')->isAvailable()) {
				$wysiwyg_class .= 'ckeditor';
			}
			else if(rex_config::get('d2u_helper', 'editor') == 'markitup' && rex_addon::get('markitup')->isAvailable()) {
				$wysiwyg_class .= 'markitupEditor-markdown_full';
			}
			else if(rex_config::get('d2u_helper', 'editor') == 'markitup_textile' && rex_addon::get('markitup')->isAvailable()) {
				$wysiwyg_class .= 'markitupEditor-textile_full';
			}
		?>
		<textarea name="REX_INPUT_VALUE[1]" class="<?php print $editor_class; ?>" >
		REX_VALUE[1]
		</textarea>
	</div>
</div>
<div class="row">
	<div class="col-xs-12">
		<br>
		Alle weiteren Einstellungen können im <a href="index.php?page=d2u_references">
				D2U Referenzen Addon</a> vorgenommen werden.
	</div>
</div>