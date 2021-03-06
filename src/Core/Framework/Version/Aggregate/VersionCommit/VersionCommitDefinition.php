<?php declare(strict_types=1);

namespace Shopware\Core\Framework\Version\Aggregate\VersionCommit;

use Shopware\Core\Defaults;
use Shopware\Core\Framework\DataAbstractionLayer\EntityDefinition;
use Shopware\Core\Framework\DataAbstractionLayer\Field\BoolField;
use Shopware\Core\Framework\DataAbstractionLayer\Field\FkField;
use Shopware\Core\Framework\DataAbstractionLayer\Field\Flag\CascadeDelete;
use Shopware\Core\Framework\DataAbstractionLayer\Field\Flag\PrimaryKey;
use Shopware\Core\Framework\DataAbstractionLayer\Field\Flag\Required;
use Shopware\Core\Framework\DataAbstractionLayer\Field\Flag\SearchRanking;
use Shopware\Core\Framework\DataAbstractionLayer\Field\Flag\WriteProtected;
use Shopware\Core\Framework\DataAbstractionLayer\Field\IdField;
use Shopware\Core\Framework\DataAbstractionLayer\Field\IntField;
use Shopware\Core\Framework\DataAbstractionLayer\Field\ManyToOneAssociationField;
use Shopware\Core\Framework\DataAbstractionLayer\Field\OneToManyAssociationField;
use Shopware\Core\Framework\DataAbstractionLayer\Field\StringField;
use Shopware\Core\Framework\DataAbstractionLayer\FieldCollection;
use Shopware\Core\Framework\DataAbstractionLayer\Write\EntityExistence;
use Shopware\Core\Framework\Version\Aggregate\VersionCommitData\VersionCommitDataDefinition;
use Shopware\Core\Framework\Version\VersionDefinition;

class VersionCommitDefinition extends EntityDefinition
{
    public const ENTITY_NAME = 'version_commit';

    public function getEntityName(): string
    {
        return self::ENTITY_NAME;
    }

    public function isVersionAware(): bool
    {
        return false;
    }

    public function getCollectionClass(): string
    {
        return VersionCommitCollection::class;
    }

    public function getEntityClass(): string
    {
        return VersionCommitEntity::class;
    }

    public function getDefaults(EntityExistence $existence): array
    {
        return [
            'name' => 'auto-save',
            'createdAt' => date(Defaults::STORAGE_DATE_TIME_FORMAT),
        ];
    }

    protected function getParentDefinitionClass(): ?string
    {
        return VersionDefinition::class;
    }

    protected function defineFields(): FieldCollection
    {
        return new FieldCollection([
            (new IdField('id', 'id'))->addFlags(new PrimaryKey(), new Required()),
            (new FkField('version_id', 'versionId', VersionDefinition::class))->addFlags(new Required()),
            new IdField('user_id', 'userId'),
            new IdField('integration_id', 'integrationId'),
            (new IntField('auto_increment', 'autoIncrement'))->addFlags(new WriteProtected()),
            new BoolField('is_merge', 'isMerge'),
            (new StringField('message', 'message'))->addFlags(new SearchRanking(SearchRanking::HIGH_SEARCH_RANKING)),
            (new OneToManyAssociationField('data', VersionCommitDataDefinition::class, 'version_commit_id'))->addFlags(new CascadeDelete()),
            new ManyToOneAssociationField('version', 'version_id', VersionDefinition::class, 'id', false),
        ]);
    }
}
