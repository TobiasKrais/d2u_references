<?php
/**
 * Redaxo D2U References Addon
 * @author Tobias Krais
 * @author <a href="http://www.design-to-use.de">www.design-to-use.de</a>
 */

/**
 * Tag
 */
class Tag {
	/**
	 * @var int Database ID
	 */
	var $tag_id = 0;
	
	/**
	 * @var int Redaxo clang id
	 */
	var $clang_id = 0;
	
	/**
	 * @var string Name
	 */
	var $name = "";
	
	/**
	 * @var string picture file name
	 */
	var $picture = "";
	
	/**
	 * @var int[] Reference IDs
	 */
	var $tag_ids = [];

	/**
	 * @var string "yes" if translation needs update
	 */
	var $translation_needs_update = "delete";

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
	 * @param int $tag_id Tag ID.
	 * @param int $clang_id Redaxo clang id.
	 */
	 public function __construct($tag_id, $clang_id) {
		$this->clang_id = $clang_id;
		$query = "SELECT * FROM ". rex::getTablePrefix() ."d2u_references_tags AS tags "
				."LEFT JOIN ". rex::getTablePrefix() ."d2u_references_tags_lang AS lang "
					."ON tags.tag_id = lang.tag_id AND clang_id = ". $this->clang_id ." "
				."WHERE tags.tag_id = ". $tag_id;
		$result = rex_sql::factory();
		$result->setQuery($query);
		$num_rows = $result->getRows();

		if ($num_rows > 0) {
			$this->tag_id = $result->getValue("tag_id");
			$this->name = $result->getValue("name");
			$this->picture = $result->getValue("picture");
			if($result->getValue("translation_needs_update") != "") {
				$this->translation_needs_update = $result->getValue("translation_needs_update");
			}
			$this->updatedate = $result->getValue("updatedate");
			$this->updateuser = $result->getValue("updateuser");
			
			$query_refs = "SELECT tag2refs.tag_id FROM ". rex::getTablePrefix() ."d2u_references_tag2refs AS tag2refs "
				."LEFT JOIN ". rex::getTablePrefix() ."d2u_references_tags_lang AS lang "
					."ON tag2refs.tag_id = lang.tag_id "
				."WHERE tag_id = ". $this->tag_id ." AND clang_id = ". $this->clang_id ." "
				."ORDER BY name";
			$result_refs = rex_sql::factory();
			$result_refs->setQuery($query_refs);
			for($i = 0; $i < $result_refs->getRows(); $i++) {
				$this->tag_ids[] = $result_refs->getValue("tag_id");
				$result_refs->next();
			}
		}
	}
	
	/**
	 * Deletes the object in all languages.
	 * @param int $delete_all If TRUE, all translations and main object are deleted. If 
	 * FALSE, only this translation will be deleted.
	 */
	public function delete($delete_all = TRUE) {
		$query_lang = "DELETE FROM ". rex::getTablePrefix() ."d2u_references_tags_lang "
			."WHERE tag_id = ". $this->tag_id
			. ($delete_all ? '' : ' AND clang_id = '. $this->clang_id) ;
		$result_lang = rex_sql::factory();
		$result_lang->setQuery($query_lang);
		
		// If no more lang objects are available, delete
		$query_main = "SELECT * FROM ". rex::getTablePrefix() ."d2u_references_tags_lang "
			."WHERE tag_id = ". $this->tag_id;
		$result_main = rex_sql::factory();
		$result_main->setQuery($query_main);
		if($result_main->getRows() == 0) {
			$query = "DELETE FROM ". rex::getTablePrefix() ."d2u_references_tags "
				."WHERE tag_id = ". $this->tag_id;
			$result = rex_sql::factory();
			$result->setQuery($query);

			$query_tags = "DELETE FROM ". rex::getTablePrefix() ."d2u_references_tag2refs "
				."WHERE tag_id = ". $this->tag_id;
			$result_tags = rex_sql::factory();
			$result_tags->setQuery($query_tags);
		}
	}
	
	/**
	 * Get all tags.
	 * @param int $clang_id Redaxo clang id.
	 * @param boolean $online_only Only tags that are used.
	 * @return Tag[] Array with Tag objects.
	 */
	public static function getAll($clang_id, $online_only = FALSE) {
		$query = "SELECT tag_id FROM ". rex::getTablePrefix() ."d2u_references_tags_lang "
			."WHERE clang_id = ". $clang_id ." "
			."ORDER BY name";
		if($online_only) {
			$query = "SELECT tag2refs.tag_id FROM ". rex::getTablePrefix() ."d2u_references_tag2refs AS tag2refs "
				."LEFT JOIN ". rex::getTablePrefix() ."d2u_references_tags_lang AS lang "
					."ON tag2refs.tag_id = lang.tag_id AND clang_id = ". $clang_id ." "
				."WHERE lang.tag_id IS NOT NULL "
				."GROUP BY tag2refs.tag_id "
				."ORDER BY name";
		}
		$result = rex_sql::factory();
		$result->setQuery($query);
		
		$tags = [];
		for($i = 0; $i < $result->getRows(); $i++) {
			$tags[$result->getValue("tag_id")] = new Tag($result->getValue("tag_id"), $clang_id);
			$result->next();
		}
		return $tags;
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
				$reference = new Tag($this->tag_id, $rex_clang->getId());
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
		return '<meta name="description" content="'. $this->name .'">';
	}
	
	/**
	 * Get all References for this Tag.
	 * @param boolean $online_only If TRUE, only online References are returned..
	 * @return Reference[] Array with Reference objects.
	 */
	public function getReferences($online_only = TRUE) {
		$query = "SELECT tag2refs.tag_id FROM ". rex::getTablePrefix() ."d2u_references_tag2refs AS tag2refs "
			."LEFT JOIN ". rex::getTablePrefix() ."d2u_references_tags AS refs "
				."ON tag2refs.tag_id = refs.tag_id "
			."WHERE tag_id = ". $this->tag_id ." ";
		if($online_only) {
			$query .= "AND online_status = 'online' ";
		}
		$query .= "GROUP BY tag_id "
			."ORDER BY `date` DESC";
		$result = rex_sql::factory();
		$result->setQuery($query);
		
		$references = [];
		for($i = 0; $i < $result->getRows(); $i++) {
			$references[$result->getValue("tag_id")] = new Reference($result->getValue("tag_id"), $this->clang_id);
			$result->next();
		}
		return $references;
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
			$parameterArray['tag_id'] = $this->tag_id;
			
			$this->url = rex_getUrl($d2u_references->getConfig('article_id'), $this->clang_id, $parameterArray, "&");
		}

		if($including_domain) {
			return str_replace(rex::getServer(). '/', rex::getServer(), rex::getServer() . $this->url);
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
		$pre_save_tag = new Tag($this->tag_id, $this->clang_id);
	
		if($this->tag_id == 0 || $pre_save_tag != $this) {
			$query = rex::getTablePrefix() ."d2u_references_tags SET "
					."picture = '". $this->picture ."' ";

			if($this->tag_id == 0) {
				$query = "INSERT INTO ". $query;
			}
			else {
				$query = "UPDATE ". $query ." WHERE tag_id = ". $this->tag_id;
			}

			$result = rex_sql::factory();
			$result->setQuery($query);
			if($this->tag_id == 0) {
				$this->tag_id = $result->getLastId();
				$error = $result->hasError();
			}

			// Save reference links
			$query_del_refs = "DELETE FROM ". rex::getTablePrefix() ."d2u_references_tag2refs WHERE tag_id = ". $this->tag_id;
			$result_del_refs = rex_sql::factory();
			$result_del_refs->setQuery($query_del_refs);
				
			foreach($this->tag_ids as $tag_id) {
				$query_add_refs = "REPLACE INTO ". rex::getTablePrefix() ."d2u_references_tag2refs SET tag_id = ". $tag_id .", tag_id = ". $this->tag_id;
				$result_add_tags = rex_sql::factory();
				$result_add_tags->setQuery($query_add_refs);
			}
		}
		
		if($error == 0) {
			// Save the language specific part
			$pre_save_tag = new Tag($this->tag_id, $this->clang_id);
			if($pre_save_tag != $this) {
				$query = "REPLACE INTO ". rex::getTablePrefix() ."d2u_references_tags_lang SET "
						."tag_id = '". $this->tag_id ."', "
						."clang_id = '". $this->clang_id ."', "
						."name = '". $this->name ."', "
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