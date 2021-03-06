<?php declare(strict_types=1);

namespace Shopware\Core\Framework\Test\DataAbstractionLayer\Search\Aggregation;

use PHPUnit\Framework\TestCase;
use Shopware\Core\Framework\Context;
use Shopware\Core\Framework\DataAbstractionLayer\Search\Aggregation\ValueCountAggregation;
use Shopware\Core\Framework\DataAbstractionLayer\Search\AggregationResult\AggregationResult;
use Shopware\Core\Framework\DataAbstractionLayer\Search\AggregationResult\ValueCountItem;
use Shopware\Core\Framework\DataAbstractionLayer\Search\AggregationResult\ValueCountResult;
use Shopware\Core\Framework\DataAbstractionLayer\Search\Criteria;
use Shopware\Core\Framework\DataAbstractionLayer\Search\Filter\EqualsAnyFilter;
use Shopware\Core\Framework\Test\TestCaseBase\AggregationTestBehaviour;
use Shopware\Core\Framework\Test\TestCaseBase\IntegrationTestBehaviour;

class ValueCountAggregationTest extends TestCase
{
    use IntegrationTestBehaviour;
    use AggregationTestBehaviour;

    public function testValueCountAggregation(): void
    {
        $context = Context::createDefaultContext();
        $ids = $this->setupFixtures($context);

        $criteria = new Criteria();
        $criteria->addFilter(new EqualsAnyFilter('id', $ids));
        $criteria->addAggregation(new ValueCountAggregation('taxRate', 'rate_agg'));

        $taxRepository = $this->getContainer()->get('tax.repository');
        $result = $taxRepository->aggregate($criteria, $context);

        $expectedValues = new ValueCountResult(null, [
            new ValueCountItem(10, 3),
            new ValueCountItem(20, 2),
            new ValueCountItem(50, 2),
            new ValueCountItem(90, 1),
        ]);

        /** @var AggregationResult $rateAgg */
        $rateAgg = $result->getAggregations()->get('rate_agg');
        static::assertNotNull($rateAgg);

        $expectedValues = [
            '10' => 3,
            '20' => 2,
            '50' => 2,
            '90' => 1,
        ];

        /** @var ValueCountResult $result */
        $result = $rateAgg->getResult()[0];

        foreach ($result->getValues() as $row) {
            $key = $row->getKey();
            static::assertArrayHasKey((string) $key, $expectedValues);
            static::assertSame($expectedValues[$key], $row->getCount());
        }
    }

    public function testValueAggregationWithGroupBy(): void
    {
        $context = Context::createDefaultContext();
        $ids = $this->setupGroupByFixtures($context);

        $criteria = new Criteria();
        $criteria->addFilter(new EqualsAnyFilter('product.categories.id', $ids));
        $criteria->addAggregation(new ValueCountAggregation('product.price.gross', 'value_agg', 'product.categories.name'));

        $productRepository = $this->getContainer()->get('product.repository');
        $result = $productRepository->aggregate($criteria, $context);

        /** @var AggregationResult $valueAgg */
        $valueAgg = $result->getAggregations()->get('value_agg');
        static::assertCount(4, $valueAgg->getResult());

        static::assertEquals(new ValueCountItem(10, 2), $valueAgg->get(['product.categories.name' => 'cat1'])->get(10));
        static::assertEquals(new ValueCountItem(20, 1), $valueAgg->get(['product.categories.name' => 'cat1'])->get(20));
        static::assertEquals(new ValueCountItem(20, 1), $valueAgg->get(['product.categories.name' => 'cat2'])->get(20));
        static::assertEquals(new ValueCountItem(50, 1), $valueAgg->get(['product.categories.name' => 'cat2'])->get(50));
        static::assertEquals(new ValueCountItem(90, 1), $valueAgg->get(['product.categories.name' => 'cat2'])->get(90));
        static::assertEquals(new ValueCountItem(10, 1), $valueAgg->get(['product.categories.name' => 'cat3'])->get(10));
        static::assertEquals(new ValueCountItem(50, 1), $valueAgg->get(['product.categories.name' => 'cat3'])->get(50));
        static::assertEquals(new ValueCountItem(90, 1), $valueAgg->get(['product.categories.name' => 'cat3'])->get(90));
        static::assertEquals(new ValueCountItem(10, 1), $valueAgg->get(['product.categories.name' => 'cat4'])->get(10));
        static::assertEquals(new ValueCountItem(20, 1), $valueAgg->get(['product.categories.name' => 'cat4'])->get(20));
    }
}
