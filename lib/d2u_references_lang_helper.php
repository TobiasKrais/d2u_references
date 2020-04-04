<?php
/**
 * Offers helper functions for language issues
 */
class d2u_references_lang_helper extends \D2U_Helper\ALangHelper {
	/**
	 * @var string[] Array with english replacements. Key is the wildcard,
	 * value the replacement. 
	 */
	protected $replacements_english = [
		'd2u_references_all_tags' => 'All tags',
		'd2u_references_external_url' => 'Got to product site',
		'd2u_references_references' => 'References',
	];

	/**
	 * @var string[] Array with german replacements. Key is the wildcard,
	 * value the replacement. 
	 */
	protected $replacements_francaise = [
		'd2u_references_all_tags' => 'Tous les tags',
		'd2u_references_external_url' => 'À la page du produit',
		'd2u_references_references' => 'Références',
	];

	/**
	 * @var string[] Array with german replacements. Key is the wildcard,
	 * value the replacement. 
	 */
	protected $replacements_german = [
		'd2u_references_all_tags' => 'Alle Tags',
		'd2u_references_external_url' => 'Zur Produktseite',
		'd2u_references_references' => 'Referenzen',
	];

	/**
	 * @var string[] Array with spanish replacements. Key is the wildcard,
	 * value the replacement. 
	 */
	protected $replacements_spanish = [
		'd2u_references_all_tags' => 'Todas las etiquetas',
		'd2u_references_external_url' => 'Ir a ubicación del producto',
		'd2u_references_references' => 'Referencias',
	];
	
	/**
	 * @var string[] Array with russian replacements. Key is the wildcard,
	 * value the replacement. 
	 */
	protected $replacements_russian = [
		'd2u_references_all_tags' => 'все теги',
		'd2u_references_external_url' => 'Перейти на сайт',
		'd2u_references_references' => 'Референции',
	];
	
	/**
	 * @var string[] Array with german replacements. Key is the wildcard,
	 * value the replacement. 
	 */
	protected $replacements_slovak = [
		'd2u_references_all_tags' => 'Všetky tagy',
		'd2u_references_external_url' => 'Stránka produktu',
		'd2u_references_references' => 'Referencie',
	];
	
	/**
	 * Factory method.
	 * @return d2u_immo_lang_helper Object
	 */
	public static function factory() {
		return new d2u_references_lang_helper();
	}
	
	/**
	 * Installs the replacement table for this addon.
	 */
	public function install() {
		foreach($this->replacements_english as $key => $value) {
			foreach (rex_clang::getAllIds() as $clang_id) {
				$lang_replacement = rex_config::get('d2u_references', 'lang_replacement_'. $clang_id, '');

				// Load values for input
				if($lang_replacement === 'german' && isset($this->replacements_german) && isset($this->replacements_german[$key])) {
					$value = $this->replacements_german[$key];
				}
				else if($lang_replacement === 'french' && isset($this->replacements_francaise) && isset($this->replacements_francaise[$key])) {
					$value = $this->replacements_francaise[$key];
				}
				else if($lang_replacement === 'russian' && isset($this->$replacements_russian) && isset($this->$replacements_russian[$key])) {
					$value = $this->$replacements_russian[$key];
				}
				else if($lang_replacement === 'slovak' && isset($this->replacements_slovak) && isset($this->replacements_slovak[$key])) {
					$value = $this->replacements_slovak[$key];
				}
				else { 
					$value = $this->replacements_english[$key];
				}

				$overwrite = rex_config::get('d2u_references', 'lang_wildcard_overwrite', FALSE) === "true" ? TRUE : FALSE;
				parent::saveValue($key, $value, $clang_id, $overwrite);
			}
		}
	}
}