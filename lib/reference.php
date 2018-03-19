<?php
/**
 * Redaxo D2U References Addon
 * @author Tobias Krais
 * @author <a href="http://www.design-to-use.de">www.design-to-use.de</a>
 */

/**
 * Reference
 */
class Reference implements \D2U_Helper\ITranslationHelper {
	/**
	 * @var int Database ID
	 */
	var $reference_id = 0;
	
	/**
	 * @var int Redaxo clang id
	 */
	var $clang_id = 0;
	
	/**
	 * @var string Name
	 */
	var $name = "";
	
	/**
	 * @var string Short description
	 */
	var $teaser = "";
	
	/**
	 * @var string Description
	 */
	var $description = "";
	
	/**
	 * @var string Online status. Either "online", "offline" or "archived".
	 */
	var $online_status = "";

	/**
	 * @var string[] Array with picture file names 
	 */
	var $pictures = [];
	
	/**
	 * @var Video Videomanager Video
	 */
	var $video = FALSE;

	/**
	 * @var string External URL 
	 */
	var $external_url = "";
	
	/**
	 * @var string Language specific external URL 
	 */
	var $external_url_lang = "";
	
	/**
	 * @var int[] Array with tags 
	 */
	var $tag_ids = [];
	
	/**
	 * @var string "yes" if translation needs update
	 */
	var $translation_needs_update = "delete";

	/**
	 * @var string Date in format YYYY-MM-DD.
	 */
	var $date = "";
	
	/**
	 * @var int Unix timestamp containing the last update date
	 */
	var $updatedate = 0;
	
	/**
	 * @var string Redaxo update user name
	 */
	var $updateuser = "";
	
	/**
	 * @var string URL
	 */
	var $url = "";

	/**
	 * Constructor. Reads the object stored in database.
	 * @param int $reference_id Reference ID.
	 * @param int $clang_id Redaxo clang id.
	 */
	 public function __construct($reference_id, $clang_id) {
		$this->clang_id = $clang_id;
		$query = "SELECT * FROM ". rex::getTablePrefix() ."d2u_references_references AS refs "
				."LEFT JOIN ". rex::getTablePrefix() ."d2u_references_references_lang AS lang "
					."ON refs.reference_id = lang.reference_id "
					."AND clang_id = ". $this->clang_id ." "
				."WHERE refs.reference_id = ". $reference_id;
		$result = rex_sql::factory();
		$result->setQuery($query);
		$num_rows = $result->getRows();

		if ($num_rows > 0) {
			$this->reference_id = $result->getValue("reference_id");
			$this->name = $result->getValue("name");
			$this->teaser = stripslashes(htmlspecialchars_decode($result->getValue("teaser")));
			$this->description = stripslashes(htmlspecialchars_decode($result->getValue("description")));
			$this->external_url = $result->getValue("url");
			$this->external_url_lang = $result->getValue("url_lang");
			$this->online_status = $result->getValue("online_status");
			$this->pictures = preg_grep('/^\s*$/s', explode(",", $result->getValue("pictures")), PREG_GREP_INVERT);
			if(rex_addon::get('d2u_videos')->isAvailable() && $result->getValue("video_id") > 0) {
				$this->video = new Video($result->getValue("video_id"), $clang_id, TRUE);
			}
			if($result->getValue("translation_needs_update") != "") {
				$this->translation_needs_update = $result->getValue("translation_needs_update");
			}
			$this->date = $result->getValue("date");
			$this->updatedate = $result->getValue("updatedate");
			$this->updateuser = $result->getValue("updateuser");
			
			$query_tags = "SELECT tag_refs.tag_id FROM ". rex::getTablePrefix() ."d2u_references_tag2refs AS tag_refs "
				."LEFT JOIN ". rex::getTablePrefix() ."d2u_references_tags_lang AS lang "
					."ON tag_refs.tag_id = lang.tag_id AND lang.clang_id = ". $this->clang_id ." "
				."WHERE reference_id = ". $this->reference_id ." AND lang.name IS NOT NULL "
				."ORDER BY lang.name";
			$result_tags = rex_sql::factory();
			$result_tags->setQuery($query_tags);
			for($i = 0; $i < $result_tags->getRows(); $i++) {
				$this->tag_ids[] = $result_tags->getValue("tag_id");
				$result_tags->next();
			}
		}
	}
	
	/**
	 * Changes the online status of this object
	 */
	public function changeStatus() {
		if($this->online_status == "online") {
			if($this->reference_id > 0) {
				$query = "UPDATE ". rex::getTablePrefix() ."d2u_references_references "
					."SET online_status = 'offline' "
					."WHERE reference_id = ". $this->reference_id;
				$result = rex_sql::factory();
				$result->setQuery($query);
			}
			$this->online_status = "offline";
		}
		else {
			if($this->reference_id > 0) {
				$query = "UPDATE ". rex::getTablePrefix() ."d2u_references_references "
					."SET online_status = 'online' "
					."WHERE reference_id = ". $this->reference_id;
				$result = rex_sql::factory();
				$result->setQuery($query);
			}
			$this->online_status = "online";			
		}
	}

	/**
	 * Deletes the object in all languages.
	 * @param boolean $delete_all If TRUE, all translations and main object are deleted. If 
	 * FALSE, only this translation will be deleted.
	 */
	public function delete($delete_all = TRUE) {
		$query_lang = "DELETE FROM ". rex::getTablePrefix() ."d2u_references_references_lang "
			."WHERE reference_id = ". $this->reference_id
			. ($delete_all ? '' : ' AND clang_id = '. $this->clang_id) ;
		$result_lang = rex_sql::factory();
		$result_lang->setQuery($query_lang);
		
		// If no more lang objects are available, delete
		$query_main = "SELECT * FROM ". rex::getTablePrefix() ."d2u_references_references_lang "
			."WHERE reference_id = ". $this->reference_id;
		$result_main = rex_sql::factory();
		$result_main->setQuery($query_main);
		if($result_main->getRows() == 0) {
			$query = "DELETE FROM ". rex::getTablePrefix() ."d2u_references_references "
				."WHERE reference_id = ". $this->reference_id;
			$result = rex_sql::factory();
			$result->setQuery($query);

			$query_tags = "DELETE FROM ". rex::getTablePrefix() ."d2u_references_tag2refs "
				."WHERE reference_id = ". $this->reference_id;
			$result_tags = rex_sql::factory();
			$result_tags->setQuery($query_tags);
		}
	}
	
	/**
	 * Get all references.
	 * @param int $clang_id Redaxo clang id.
	 * @param boolean $online_only If TRUE, only online References are returned..
	 * @return Reference[] Array with Reference objects.
	 */
	public static function getAll($clang_id, $online_only = TRUE) {
		$query = "SELECT lang.reference_id FROM ". rex::getTablePrefix() ."d2u_references_references_lang AS lang "
			."LEFT JOIN ". rex::getTablePrefix() ."d2u_references_references AS refs "
				."ON lang.reference_id = refs.reference_id "
			."WHERE clang_id = ". $clang_id ." ";
		if($online_only) {
			$query .= "AND online_status = 'online' ";
		}
		$query .= 'ORDER BY `date` DESC';
		$result = rex_sql::factory();
		$result->setQuery($query);
		
		$references = [];
		for($i = 0; $i < $result->getRows(); $i++) {
			$references[$result->getValue("reference_id")] = new Reference($result->getValue("reference_id"), $clang_id);
			$result->next();
		}
		return $references;
	}
	
	/**
	 * Get the <link rel="canonical"> tag for page header.
	 * @return Complete tag.
	 */
	public function getCanonicalTag() {
		return '<link rel="canonical" href="'. $this->getURL() .'">';
	}
	
	/**
	 * Get the <title> tag for page header.
	 * @return Complete title tag.
	 */
	public function getTitleTag() {
		return '<title>'. $this->name .' / '. rex::getServerName() .'</title>';
	}
	
	/**
	 * Get the <meta rel="alternate" hreflang=""> tags for page header.
	 * @return Complete tags.
	 */
	public function getMetaAlternateHreflangTags() {
		$hreflang_tags = "";
		foreach(rex_clang::getAll() as $rex_clang) {
			if($rex_clang->getId() == $this->clang_id && $this->translation_needs_update != "delete") {
				$hreflang_tags .= '<link rel="alternate" type="text/html" hreflang="'. $rex_clang->getCode() .'" href="'. $this->getURL() .'" title="'. str_replace('"', '', $this->name) .'">';
			}
			else {
				$reference = new Reference($this->reference_id, $rex_clang->getId());
				if($reference->translation_needs_update != "delete") {
					$hreflang_tags .= '<link rel="alternate" type="text/html" hreflang="'. $rex_clang->getCode() .'" href="'. $reference->getURL() .'" title="'. str_replace('"', '', $reference->name) .'">';
				}
			}
		}
		return $hreflang_tags;
	}
	
	/**
	 * Get the <meta name="description"> tag for page header.
	 * @return Complete tag.
	 */
	public function getMetaDescriptionTag() {
		return '<meta name="description" content="'. strip_tags($this->teaser) .'">';
	}
	
	/**
	 * Get objects concerning translation updates
	 * @param int $clang_id Redaxo language ID
	 * @param string $type 'update' or 'missing'
	 * @return Reference[] Array with Reference objects.
	 */
	public static function getTranslationHelperObjects($clang_id, $type) {
		$query = 'SELECT reference_id FROM '. \rex::getTablePrefix() .'d2u_references_references_lang '
				."WHERE clang_id = ". $clang_id ." AND translation_needs_update = 'yes' "
				.'ORDER BY name';
		if($type == 'missing') {
			$query = 'SELECT main.reference_id FROM '. \rex::getTablePrefix() .'d2u_references_references AS main '
					.'LEFT JOIN '. \rex::getTablePrefix() .'d2u_references_references_lang AS target_lang '
						.'ON main.reference_id = target_lang.reference_id AND target_lang.clang_id = '. $clang_id .' '
					.'LEFT JOIN '. \rex::getTablePrefix() .'d2u_references_references_lang AS default_lang '
						.'ON main.reference_id = default_lang.reference_id AND default_lang.clang_id = '. \rex_config::get('d2u_helper', 'default_lang') .' '
					."WHERE target_lang.reference_id IS NULL "
					.'ORDER BY default_lang.name';
			$clang_id = \rex_config::get('d2u_helper', 'default_lang');
		}
		$result = \rex_sql::factory();
		$result->setQuery($query);

		$objects = [];
		for($i = 0; $i < $result->getRows(); $i++) {
			$objects[] = new Reference($result->getValue("reference_id"), $clang_id);
			$result->next();
		}
		
		return $objects;
    }
	
	/*
	 * Returns the URL of this object.
	 * @param string $including_domain TRUE if Domain name should be included
	 * @return string URL
	 */
	public function getURL($including_domain = FALSE) {
		if($this->url == "") {
			$d2u_references = rex_addon::get("d2u_references");
				
			$parameterArray = [];
			$parameterArray['reference_id'] = $this->reference_id;
			
			$this->url = rex_getUrl($d2u_references->getConfig('article_id'), $this->clang_id, $parameterArray, "&");
		}

		if($including_domain) {
			if(rex_addon::get('yrewrite')->isAvailable())  {
				return str_replace(rex_yrewrite::getCurrentDomain()->getUrl() .'/', rex_yrewrite::getCurrentDomain()->getUrl(), rex_yrewrite::getCurrentDomain()->getUrl() . $this->url);
			}
			else {
				return str_replace(\rex::getServer(). '/', \rex::getServer(), \rex::getServer() . $this->url);
			}
		}
		else {
			return $this->url;
		}
	}
	
	/**
	 * Updates or inserts the object into database.
	 * @return boolean TRUE if error occured
	 */
	public function save() {
		$error = 0;

		// Save the not language specific part
		$pre_save_reference = new Reference($this->reference_id, $this->clang_id);
	
		if($this->reference_id == 0 || $pre_save_reference != $this) {
			$query = rex::getTablePrefix() ."d2u_references_references SET "
					."online_status = '". $this->online_status ."', "
					."pictures = '". implode(",", $this->pictures) ."', "
					."video_id = ". ($this->video !== FALSE ? $this->video->video_id : 0) .", "
					."url = '". $this->external_url ."', "
					."`date` = '". $this->date ."' ";

			if($this->reference_id == 0) {
				$query = "INSERT INTO ". $query;
			}
			else {
				$query = "UPDATE ". $query ." WHERE reference_id = ". $this->reference_id;
			}

			$result = rex_sql::factory();
			$result->setQuery($query);
			if($this->reference_id == 0) {
				$this->reference_id = $result->getLastId();
				$error = $result->hasError();
			}

			// Save tag links
			$query_del_tags = "DELETE FROM ". rex::getTablePrefix() ."d2u_references_tag2refs WHERE reference_id = ". $this->reference_id;
			$result_del_tags = rex_sql::factory();
			$result_del_tags->setQuery($query_del_tags);
				
			foreach($this->tag_ids as $tag_id) {
				$query_add_tags = "INSERT INTO ". rex::getTablePrefix() ."d2u_references_tag2refs SET reference_id = ". $this->reference_id .", tag_id = ". $tag_id;
				$result_add_tags = rex_sql::factory();
				$result_add_tags->setQuery($query_add_tags);
			}
		}
		
		if($error == 0) {
			// Save the language specific part
			$pre_save_reference = new Reference($this->reference_id, $this->clang_id);
			if($pre_save_reference != $this) {
				$query = "REPLACE INTO ". rex::getTablePrefix() ."d2u_references_references_lang SET "
						."reference_id = '". $this->reference_id ."', "
						."clang_id = '". $this->clang_id ."', "
						."name = '". $this->name ."', "
						."teaser = '". addslashes(htmlspecialchars($this->teaser)) ."', "
						."description = '". addslashes(htmlspecialchars($this->description)) ."', "
						."url_lang = '". $this->external_url_lang ."', "
						."translation_needs_update = '". $this->translation_needs_update ."', "
						."updatedate = ". time() .", "
						."updateuser = '". rex::getUser()->getLogin() ."' ";

				$result = rex_sql::factory();
				$result->setQuery($query);
				$error = $result->hasError();
			}
		}
		
		// Update URLs
		if(rex_addon::get("url")->isAvailable()) {
			UrlGenerator::generatePathFile([]);
		}
		
		return $error;
	}
}