<?php

namespace TobiasKrais\D2UReferences\Api;

use rex_addon;
use rex_clang;
use TobiasKrais\D2UReferences\Reference;
use TobiasKrais\D2UReferences\Tag;

/**
 * Single source of truth for the d2u_references REST API.
 *
 * Describes every writable/readable resource field and whether it is language
 * specific. The same definition drives the discovery endpoint, the write
 * validation and the payload-to-model mapping.
 */
final class Schema
{
    /**
     * Resource meta: model class, id field and whether save() returns the error
     * flag (true on error) instead of a success flag (true on success).
     *
     * @var array<string,array{class:class-string,id:string,save_error_flag:bool}>
     */
    private const RESOURCES = [
        'references' => ['class' => Reference::class, 'id' => 'reference_id', 'save_error_flag' => true],
        'tags' => ['class' => Tag::class, 'id' => 'tag_id', 'save_error_flag' => true],
    ];

    /**
     * Field definitions per resource.
     *
     * Each field: type, required (on create), language (per clang), relation
     * (target resource for id references), seo (frontend meta role).
     *
     * @var array<string,array<string,array{type:string,required?:bool,language?:bool,relation?:string,seo?:string}>>
     */
    private const FIELDS = [
        'references' => [
            'online_status' => ['type' => 'enum:online,offline,archived'],
            'pictures' => ['type' => 'media[]', 'seo' => 'image'],
            'background_color' => ['type' => 'string'],
            'background_color_dark' => ['type' => 'string'],
            'video_id' => ['type' => 'int', 'relation' => 'videos'],
            'article_id' => ['type' => 'int'],
            'external_url' => ['type' => 'string'],
            'tag_ids' => ['type' => 'int[]', 'relation' => 'tags'],
            'date' => ['type' => 'string'],
            'name' => ['type' => 'string', 'language' => true, 'required' => true, 'seo' => 'title'],
            'teaser' => ['type' => 'html', 'language' => true, 'seo' => 'description'],
            'description' => ['type' => 'html', 'language' => true],
            'external_url_lang' => ['type' => 'string', 'language' => true],
        ],
        'tags' => [
            'picture' => ['type' => 'media'],
            'name' => ['type' => 'string', 'language' => true, 'required' => true],
            'reference_ids' => ['type' => 'int[]', 'relation' => 'references'],
        ],
    ];

    /**
     * @return array<int,string> Resource keys
     */
    public static function getAvailableResources(): array
    {
        return array_keys(self::RESOURCES);
    }

    public static function hasResource(string $resource): bool
    {
        return isset(self::RESOURCES[$resource]);
    }

    /**
     * @return array{class:class-string,id:string,save_error_flag:bool}
     */
    public static function getResourceMeta(string $resource): array
    {
        return self::RESOURCES[$resource];
    }

    /**
     * @return array<string,array{type:string,required?:bool,language?:bool,relation?:string,seo?:string}>
     */
    public static function getFields(string $resource): array
    {
        return self::FIELDS[$resource] ?? [];
    }

    public static function isLanguageField(string $resource, string $field): bool
    {
        return (bool) (self::FIELDS[$resource][$field]['language'] ?? false);
    }

    /**
     * Full machine-readable capability document for the discovery endpoint.
     *
     * @return array<string,mixed>
     */
    public static function describe(): array
    {
        $languages = [];
        foreach (rex_clang::getAll() as $clang) {
            $languages[] = [
                'id' => $clang->getId(),
                'code' => $clang->getCode(),
                'name' => $clang->getName(),
            ];
        }

        $resources = [];
        foreach (self::getAvailableResources() as $resource) {
            $fields = [];
            foreach (self::getFields($resource) as $name => $definition) {
                $fields[] = [
                    'name' => $name,
                    'type' => $definition['type'],
                    'required' => (bool) ($definition['required'] ?? false),
                    'language' => (bool) ($definition['language'] ?? false),
                    'relation' => $definition['relation'] ?? null,
                    'seo' => $definition['seo'] ?? null,
                ];
            }

            $resources[$resource] = [
                'id_field' => self::RESOURCES[$resource]['id'],
                'endpoints' => [
                    'list' => 'GET /api/d2u_references/' . $resource,
                    'get' => 'GET /api/d2u_references/' . $resource . '/{id}',
                    'create' => 'POST /api/d2u_references/' . $resource,
                    'update' => 'PATCH /api/d2u_references/' . $resource . '/{id}',
                    'delete' => 'DELETE /api/d2u_references/' . $resource . '/{id}',
                ],
                'fields' => $fields,
            ];
        }

        return [
            'addon' => 'd2u_references',
            'version' => (string) rex_addon::get('d2u_references')->getVersion(),
            'languages' => $languages,
            'notes' => [
                'Language specific fields are provided per clang inside "translations".',
                'Non-language fields are provided under "fields".',
                'Upload images via the api addon endpoint POST /api/media first, then reference the returned file name.',
                'The "seo" attribute marks how a field is used for the frontend SEO meta data: "title", "description" or "image" (og:image; for media[] the first image is used).',
                'The field "video_id" references a d2u_videos video and is only writable when the d2u_videos addon is installed.',
                'Only fields listed here may be written; unknown fields are rejected with HTTP 400.',
            ],
            'resources' => $resources,
        ];
    }
}
