<?php
/**
 * Offers helper functions for language issues.
 */
class d2u_references_lang_helper extends \D2U_Helper\ALangHelper
{
    /**
     * @var array<string, string> Array with english replacements. Key is the wildcard,
     * value the replacement.
     */
    public $replacements_english = [
        'd2u_references_all_tags' => 'All tags',
        'd2u_references_external_url' => 'Got to product site',
        'd2u_references_references' => 'References',
    ];

    /**
     * @var array<string, string> Array with german replacements. Key is the wildcard,
     * value the replacement.
     */
    protected array $replacements_francaise = [
        'd2u_references_all_tags' => 'Tous les tags',
        'd2u_references_external_url' => 'À la page du produit',
        'd2u_references_references' => 'Références',
    ];

    /**
     * @var array<string, string> Array with german replacements. Key is the wildcard,
     * value the replacement.
     */
    protected array $replacements_german = [
        'd2u_references_all_tags' => 'Alle Tags',
        'd2u_references_external_url' => 'Zur Produktseite',
        'd2u_references_references' => 'Referenzen',
    ];

    /**
     * @var array<string, string> Array with spanish replacements. Key is the wildcard,
     * value the replacement.
     */
    protected array $replacements_spanish = [
        'd2u_references_all_tags' => 'Todas las etiquetas',
        'd2u_references_external_url' => 'Ir a ubicación del producto',
        'd2u_references_references' => 'Referencias',
    ];

    /**
     * @var array<string, string> Array with russian replacements. Key is the wildcard,
     * value the replacement.
     */
    protected array $replacements_russian = [
        'd2u_references_all_tags' => 'все теги',
        'd2u_references_external_url' => 'Перейти на сайт',
        'd2u_references_references' => 'Референции',
    ];

    /**
     * @var array<string, string> Array with german replacements. Key is the wildcard,
     * value the replacement.
     */
    protected array $replacements_slovak = [
        'd2u_references_all_tags' => 'Všetky tagy',
        'd2u_references_external_url' => 'Stránka produktu',
        'd2u_references_references' => 'Referencie',
    ];

    /**
     * Factory method.
     * @return d2u_immo_lang_helper Object
     */
    public static function factory()
    {
        return new self();
    }

    /**
     * Installs the replacement table for this addon.
     */
    public function install(): void
    {
        foreach ($this->replacements_english as $key => $value) {
            foreach (rex_clang::getAllIds() as $clang_id) {
                $lang_replacement = rex_config::get('d2u_references', 'lang_replacement_'. $clang_id, '');

                // Load values for input
                if ('german' === $lang_replacement && isset($this->replacements_german) && isset($this->replacements_german[$key])) {
                    $value = $this->replacements_german[$key];
                } elseif ('french' === $lang_replacement && isset($this->replacements_francaise) && isset($this->replacements_francaise[$key])) {
                    $value = $this->replacements_francaise[$key];
                } elseif ('russian' === $lang_replacement && isset($this->$replacements_russian) && isset($this->$replacements_russian[$key])) {
                    $value = $this->$replacements_russian[$key];
                } elseif ('slovak' === $lang_replacement && isset($this->replacements_slovak) && isset($this->replacements_slovak[$key])) {
                    $value = $this->replacements_slovak[$key];
                } else {
                    $value = $this->replacements_english[$key];
                }

                $overwrite = 'true' === rex_config::get('d2u_references', 'lang_wildcard_overwrite', false) ? true : false;
                parent::saveValue($key, $value, $clang_id, $overwrite);
            }
        }
    }
}
