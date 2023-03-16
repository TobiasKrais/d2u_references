<?php
/**
 * Redaxo D2U References Addon.
 * @author Tobias Krais
 * @author <a href="http://www.design-to-use.de">www.design-to-use.de</a>
 */

/**
 * Tag.
 */
class Tag implements \D2U_Helper\ITranslationHelper
{
    /** @var int Database ID */
    public $tag_id = 0;

    /** @var int Redaxo clang id */
    public $clang_id = 0;

    /** @var string Name */
    public $name = '';

    /** @var string picture file name */
    public $picture = '';

    /** @var int[] Reference IDs */
    public $reference_ids = [];

    /** @var string "yes" if translation needs update */
    public $translation_needs_update = 'delete';

    /** @var string Timestamp containing the last update date */
    public $updatedate = '';

    /** @var string URL */
    public $url = '';

    /**
     * Constructor. Reads the object stored in database.
     * @param int $tag_id tag ID
     * @param int $clang_id redaxo clang id
     */
    public function __construct($tag_id, $clang_id)
    {
        $this->clang_id = $clang_id;
        $query = 'SELECT * FROM '. rex::getTablePrefix() .'d2u_references_tags AS tags '
                .'LEFT JOIN '. rex::getTablePrefix() .'d2u_references_tags_lang AS lang '
                    .'ON tags.tag_id = lang.tag_id AND clang_id = '. $this->clang_id .' '
                .'WHERE tags.tag_id = '. $tag_id;
        $result = rex_sql::factory();
        $result->setQuery($query);
        $num_rows = $result->getRows();

        if ($num_rows > 0) {
            $this->tag_id = $result->getValue('tag_id');
            $this->name = stripslashes($result->getValue('name'));
            $this->picture = $result->getValue('picture');
            if ('' != $result->getValue('translation_needs_update')) {
                $this->translation_needs_update = $result->getValue('translation_needs_update');
            }
            $this->updatedate = $result->getValue('updatedate');

            $query_refs = 'SELECT tag2refs.tag_id, tag2refs.reference_id FROM '. rex::getTablePrefix() .'d2u_references_tag2refs AS tag2refs '
                .'LEFT JOIN '. rex::getTablePrefix() .'d2u_references_tags_lang AS lang '
                    .'ON tag2refs.tag_id = lang.tag_id '
                .'WHERE tag2refs.tag_id = '. $this->tag_id .' AND clang_id = '. $this->clang_id .' '
                .'ORDER BY name';
            $result_refs = rex_sql::factory();
            $result_refs->setQuery($query_refs);
            for ($i = 0; $i < $result_refs->getRows(); ++$i) {
                $this->reference_ids[] = $result_refs->getValue('reference_id');
                $result_refs->next();
            }
        }
    }

    /**
     * Deletes the object in all languages.
     * @param bool $delete_all If true, all translations and main object are deleted. If
     * false, only this translation will be deleted.
     */
    public function delete($delete_all = true): void
    {
        $query_lang = 'DELETE FROM '. rex::getTablePrefix() .'d2u_references_tags_lang '
            .'WHERE tag_id = '. $this->tag_id
            . ($delete_all ? '' : ' AND clang_id = '. $this->clang_id);
        $result_lang = rex_sql::factory();
        $result_lang->setQuery($query_lang);

        // If no more lang objects are available, delete
        $query_main = 'SELECT * FROM '. rex::getTablePrefix() .'d2u_references_tags_lang '
            .'WHERE tag_id = '. $this->tag_id;
        $result_main = rex_sql::factory();
        $result_main->setQuery($query_main);
        if (0 === (int) $result_main->getRows()) {
            $query = 'DELETE FROM '. rex::getTablePrefix() .'d2u_references_tags '
                .'WHERE tag_id = '. $this->tag_id;
            $result = rex_sql::factory();
            $result->setQuery($query);

            $query_tags = 'DELETE FROM '. rex::getTablePrefix() .'d2u_references_tag2refs '
                .'WHERE tag_id = '. $this->tag_id;
            $result_tags = rex_sql::factory();
            $result_tags->setQuery($query_tags);
        }
    }

    /**
     * Get all tags.
     * @param int $clang_id redaxo clang id
     * @param bool $online_only only tags that are used
     * @return Tag[] array with Tag objects
     */
    public static function getAll($clang_id, $online_only = false)
    {
        $query = 'SELECT tag_id FROM '. rex::getTablePrefix() .'d2u_references_tags_lang '
            .'WHERE clang_id = '. $clang_id .' '
            .'ORDER BY name';
        if ($online_only) {
            $query = 'SELECT tag2refs.tag_id FROM '. rex::getTablePrefix() .'d2u_references_tag2refs AS tag2refs '
                .'LEFT JOIN '. rex::getTablePrefix() .'d2u_references_tags_lang AS lang '
                    .'ON tag2refs.tag_id = lang.tag_id AND clang_id = '. $clang_id .' '
                .'WHERE lang.tag_id IS NOT NULL '
                .'GROUP BY tag2refs.tag_id '
                .'ORDER BY name';
        }
        $result = rex_sql::factory();
        $result->setQuery($query);

        $tags = [];
        for ($i = 0; $i < $result->getRows(); ++$i) {
            $tags[$result->getValue('tag_id')] = new self($result->getValue('tag_id'), $clang_id);
            $result->next();
        }
        return $tags;
    }

    /**
     * Get all References for this Tag.
     * @param bool $online_only If true, only online References are returned..
     * @return Reference[] array with Reference objects
     */
    public function getReferences($online_only = true)
    {
        $query = 'SELECT refs.reference_id FROM '. rex::getTablePrefix() .'d2u_references_tag2refs AS tag2refs '
            .'LEFT JOIN '. rex::getTablePrefix() .'d2u_references_references AS refs '
                .'ON tag2refs.reference_id = refs.reference_id '
            .'WHERE tag2refs.tag_id = '. $this->tag_id .' ';
        if ($online_only) {
            $query .= "AND online_status = 'online' ";
        }
        $query .= 'GROUP BY tag2refs.reference_id '
            .'ORDER BY `date` DESC';
        $result = rex_sql::factory();
        $result->setQuery($query);

        $references = [];
        for ($i = 0; $i < $result->getRows(); ++$i) {
            $references[$result->getValue('reference_id')] = new Reference($result->getValue('reference_id'), $this->clang_id);
            $result->next();
        }
        return $references;
    }

    /**
     * Get objects concerning translation updates.
     * @param int $clang_id Redaxo language ID
     * @param string $type 'update' or 'missing'
     * @return Tag[] array with Tag objects
     */
    public static function getTranslationHelperObjects($clang_id, $type)
    {
        $query = 'SELECT tag_id FROM '. \rex::getTablePrefix() .'d2u_references_tags_lang '
                .'WHERE clang_id = '. $clang_id ." AND translation_needs_update = 'yes' "
                .'ORDER BY name';
        if ('missing' === $type) {
            $query = 'SELECT main.tag_id FROM '. \rex::getTablePrefix() .'d2u_references_tags AS main '
                    .'LEFT JOIN '. \rex::getTablePrefix() .'d2u_references_tags_lang AS target_lang '
                        .'ON main.tag_id = target_lang.tag_id AND target_lang.clang_id = '. $clang_id .' '
                    .'LEFT JOIN '. \rex::getTablePrefix() .'d2u_references_tags_lang AS default_lang '
                        .'ON main.tag_id = default_lang.tag_id AND default_lang.clang_id = '. \rex_config::get('d2u_helper', 'default_lang') .' '
                    .'WHERE target_lang.tag_id IS NULL '
                    .'ORDER BY default_lang.name';
            $clang_id = \rex_config::get('d2u_helper', 'default_lang');
        }
        $result = \rex_sql::factory();
        $result->setQuery($query);

        $objects = [];
        for ($i = 0; $i < $result->getRows(); ++$i) {
            $objects[] = new self($result->getValue('tag_id'), $clang_id);
            $result->next();
        }

        return $objects;
    }

    /**
     * Returns the URL of this object.
     * @param bool $including_domain true if Domain name should be included
     * @return string URL
     */
    public function getUrl($including_domain = false)
    {
        if ('' === $this->url) {
            $d2u_references = rex_addon::get('d2u_references');

            $parameterArray = [];
            $parameterArray['tag_id'] = $this->tag_id;

            $this->url = rex_getUrl($d2u_references->getConfig('article_id'), $this->clang_id, $parameterArray, '&');
        }

        if ($including_domain) {
            if (\\rex_addon::get('yrewrite') instanceof \rex_addon_interface && \rex_addon::get('yrewrite')->isAvailable()) {
                return str_replace(\rex_yrewrite::getCurrentDomain()->getUrl() .'/', \rex_yrewrite::getCurrentDomain()->getUrl(), \rex_yrewrite::getCurrentDomain()->getUrl() . $this->url);
            }

            return str_replace(\rex::getServer(). '/', \rex::getServer(), \rex::getServer() . $this->url);

        }

        return $this->url;

    }

    /**
     * Updates or inserts the object into database.
     * @return bool true if error occured
     */
    public function save()
    {
        $error = 0;

        // Save the not language specific part
        $pre_save_object = new self($this->tag_id, $this->clang_id);

        if (0 === $this->tag_id || $pre_save_object != $this) {
            $query = rex::getTablePrefix() .'d2u_references_tags SET '
                    ."picture = '". $this->picture ."' ";

            if (0 === $this->tag_id) {
                $query = 'INSERT INTO '. $query;
            } else {
                $query = 'UPDATE '. $query .' WHERE tag_id = '. $this->tag_id;
            }

            $result = rex_sql::factory();
            $result->setQuery($query);
            if (0 === $this->tag_id) {
                $this->tag_id = (int) $result->getLastId();
                $error = $result->hasError();
            }

            // Save reference links
            $query_del_refs = 'DELETE FROM '. rex::getTablePrefix() .'d2u_references_tag2refs WHERE tag_id = '. $this->tag_id;
            $result_del_refs = rex_sql::factory();
            $result_del_refs->setQuery($query_del_refs);

            foreach ($this->reference_ids as $reference_id) {
                $query_add_refs = 'REPLACE INTO '. rex::getTablePrefix() .'d2u_references_tag2refs SET reference_id = '. $reference_id .', tag_id = '. $this->tag_id;
                $result_add_tags = rex_sql::factory();
                $result_add_tags->setQuery($query_add_refs);
            }
        }

        $regenerate_urls = false;
        if (0 == $error) {
            // Save the language specific part
            $pre_save_object = new self($this->tag_id, $this->clang_id);
            if ($pre_save_object != $this) {
                $query = 'REPLACE INTO '. rex::getTablePrefix() .'d2u_references_tags_lang SET '
                        ."tag_id = '". $this->tag_id ."', "
                        ."clang_id = '". $this->clang_id ."', "
                        ."name = '". addslashes($this->name) ."', "
                        ."translation_needs_update = '". $this->translation_needs_update ."', "
                        .'updatedate = CURRENT_TIMESTAMP ';

                $result = rex_sql::factory();
                $result->setQuery($query);
                $error = $result->hasError();

                if (!$error && $pre_save_object->name !== $this->name) {
                    $regenerate_urls = true;
                }
            }
        }

        // Update URLs
        if ($regenerate_urls) {
            \d2u_addon_backend_helper::generateUrlCache('tag_id');
        }

        return $error;
    }
}
