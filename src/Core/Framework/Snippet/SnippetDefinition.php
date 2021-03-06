<?php declare(strict_types=1);

namespace Shopware\Core\Framework\Snippet;

use Shopware\Core\Framework\DataAbstractionLayer\EntityDefinition;
use Shopware\Core\Framework\DataAbstractionLayer\Field\CustomFields;
use Shopware\Core\Framework\DataAbstractionLayer\Field\FkField;
use Shopware\Core\Framework\DataAbstractionLayer\Field\Flag\PrimaryKey;
use Shopware\Core\Framework\DataAbstractionLayer\Field\Flag\Required;
use Shopware\Core\Framework\DataAbstractionLayer\Field\Flag\SearchRanking;
use Shopware\Core\Framework\DataAbstractionLayer\Field\IdField;
use Shopware\Core\Framework\DataAbstractionLayer\Field\LongTextField;
use Shopware\Core\Framework\DataAbstractionLayer\Field\ManyToOneAssociationField;
use Shopware\Core\Framework\DataAbstractionLayer\Field\StringField;
use Shopware\Core\Framework\DataAbstractionLayer\FieldCollection;
use Shopware\Core\Framework\Snippet\Aggregate\SnippetSet\SnippetSetDefinition;

class SnippetDefinition extends EntityDefinition
{
    public const ENTITY_NAME = 'snippet';

    public function getEntityName(): string
    {
        return self::ENTITY_NAME;
    }

    public function getCollectionClass(): string
    {
        return SnippetCollection::class;
    }

    public function getEntityClass(): string
    {
        return SnippetEntity::class;
    }

    protected function getParentDefinitionClass(): ?string
    {
        return SnippetSetDefinition::class;
    }

    protected function defineFields(): FieldCollection
    {
        return new FieldCollection([
            (new IdField('id', 'id'))->addFlags(new PrimaryKey(), new Required()),
            (new FkField('snippet_set_id', 'setId', SnippetSetDefinition::class))->addFlags(new Required()),
            (new StringField('translation_key', 'translationKey'))->addFlags(new Required(), new SearchRanking(SearchRanking::HIGH_SEARCH_RANKING)),
            (new LongTextField('value', 'value'))->addFlags(new Required(), new SearchRanking(SearchRanking::HIGH_SEARCH_RANKING)),
            (new StringField('author', 'author'))->addFlags(new Required(), new SearchRanking(SearchRanking::HIGH_SEARCH_RANKING)),
            new CustomFields(),
            new ManyToOneAssociationField('set', 'snippet_set_id', SnippetSetDefinition::class, 'id', false),
        ]);
    }
}
