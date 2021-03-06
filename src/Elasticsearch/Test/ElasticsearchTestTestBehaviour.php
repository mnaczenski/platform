<?php declare(strict_types=1);

namespace Shopware\Elasticsearch\Test;

use Elasticsearch\Client;
use Shopware\Core\Framework\DataAbstractionLayer\Dbal\EntityAggregator;
use Shopware\Core\Framework\DataAbstractionLayer\Dbal\EntitySearcher;
use Shopware\Core\Framework\DataAbstractionLayer\DefinitionInstanceRegistry;
use Shopware\Core\Framework\Test\TestCaseBase\IntegrationTestBehaviour;
use Shopware\Core\Framework\Test\TestCaseBase\QueueTestBehaviour;
use Shopware\Elasticsearch\Framework\DataAbstractionLayer\ElasticsearchEntityAggregator;
use Shopware\Elasticsearch\Framework\DataAbstractionLayer\ElasticsearchEntitySearcher;
use Shopware\Elasticsearch\Framework\ElasticsearchHelper;
use Shopware\Elasticsearch\Framework\Indexing\CreateAliasTaskHandler;
use Shopware\Elasticsearch\Framework\Indexing\EntityIndexer;
use Symfony\Component\DependencyInjection\ContainerInterface;

trait ElasticsearchTestTestBehaviour
{
    use IntegrationTestBehaviour;
    use QueueTestBehaviour;

    /**
     * @before
     */
    public function enableElasticsearch(): void
    {
        $this->getDiContainer()
            ->get(ElasticsearchHelper::class)
            ->setEnabled(true);
    }

    /**
     * @after
     */
    public function disableElasticsearch(): void
    {
        $this->getDiContainer()
            ->get(ElasticsearchHelper::class)
            ->setEnabled(false);
    }

    public function indexElasticSearch(): void
    {
        $this->getDiContainer()
            ->get(EntityIndexer::class)
            ->index(new \DateTime());

        $this->runWorker();

        $this->getDiContainer()
            ->get(CreateAliasTaskHandler::class)
            ->run();
    }

    protected function createEntityAggregator(): ElasticsearchEntityAggregator
    {
        $decorated = $this->createMock(EntityAggregator::class);

        $decorated
            ->expects(static::never())
            ->method('aggregate');

        return new ElasticsearchEntityAggregator(
            $this->getDiContainer()->get(ElasticsearchHelper::class),
            $this->getDiContainer()->get(Client::class),
            $decorated,
            $this->getDiContainer()->get(DefinitionInstanceRegistry::class)
        );
    }

    protected function createEntitySearcher(): ElasticsearchEntitySearcher
    {
        $decorated = $this->createMock(EntitySearcher::class);

        $decorated
            ->expects(static::never())
            ->method('search');

        return new ElasticsearchEntitySearcher(
            $this->getDiContainer()->get(Client::class),
            $decorated,
            $this->getDiContainer()->get(ElasticsearchHelper::class)
        );
    }

    abstract protected function getDiContainer(): ContainerInterface;
}
