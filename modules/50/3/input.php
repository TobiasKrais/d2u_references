<div class="row">
	<div class="col-xs-4">
		Breite des Blocks (nur Detailansicht):
	</div>
	<div class="col-xs-8">
		<select name="REX_INPUT_VALUE[20]" class="form-control">
		<?php
		$values = [12=>"12 von 12 Spalten (ganze Breite)", 8=>"8 von 12 Spalten", 6=>"6 von 12 Spalten", 4=>"4 von 12 Spalten", 3=>"3 von 12 Spalten"];
		foreach($values as $key => $value) {
			echo '<option value="'. $key .'" ';
	
			if (intval("REX_VALUE[20]") === $key) { /** @phpstan-ignore-line */
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
		<select name="REX_INPUT_VALUE[17]" class="form-control">
		<?php
		$values = array(0=>"Kein Offset", 1=>"Offset");
		foreach($values as $key => $value) {
			echo '<option value="'. $key .'" ';
	
			if (intval("REX_VALUE[17]") === $key) { /** @phpstan-ignore-line */
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
		<input class="form-control" type="number" size="5" name="REX_INPUT_VALUE[2]" value="REX_VALUE[2]" />
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
		<textarea name="REX_INPUT_VALUE[1]" class="<?php print d2u_addon_backend_helper::getWYSIWYGEditorClass(); ?>" >
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